<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Exception\AppException;

uses(Tests\TestCase::class);

beforeEach(function () {
    // Reset Laravel's app instance and binding to ensure fresh state for each test
    $this->afterApplicationRefreshed(function () {
        app()->forgetInstance(\Illuminate\Contracts\Debug\ExceptionHandler::class);
    });
});

test('can be instantiated with default parameters', function () {
    $exception = new AppException('user::message.default');

    expect($exception)->toBeInstanceOf(AppException::class)
        ->and($exception->getUserMessage())->toBe('user::message.default')
        ->and($exception->getLogMessage())->toBe('user::message.default')
        ->and($exception->getCode())->toBe(422)
        ->and($exception->getContext())->toBe([]);
});

test('can be instantiated with custom parameters', function () {
    $exception = new AppException(
        userMessage: 'user::message.custom',
        replace: ['name' => 'Test'],
        locale: 'en',
        logMessage: 'Log message details',
        code: 400,
        context: ['id' => 123]
    );

    expect($exception)->toBeInstanceOf(AppException::class)
        ->and($exception->getUserMessage())->toBe('user::message.custom') // Assuming __() is mocked or translated later
        ->and($exception->getLogMessage())->toBe('Log message details')
        ->and($exception->getCode())->toBe(400)
        ->and($exception->getContext())->toBe(['id' => 123]);
});

test('returns correct user message', function () {
    // Mock the translation helper
    // In a real Laravel app, __() would translate this.
    // For unit testing, we'll ensure it returns the key or a mocked translation.
    /** @phpstan-ignore-next-line */
    $this->app->instance('translator', Mockery::mock(\Illuminate\Translation\Translator::class, function ($mock) {
        $mock->shouldReceive('getFromJson')->andReturnUsing(function ($key, $replace, $locale) {
            return str_replace(['::message', '::exception'], '', $key).(empty($replace) ? '' : ' with '.json_encode($replace)).(empty($locale) ? '' : ' in '.$locale);
        });
        $mock->shouldReceive('get')->andReturnUsing(function ($key, $replace, $locale) {
            return str_replace(['::message', '::exception'], '', $key).(empty($replace) ? '' : ' with '.json_encode($replace)).(empty($locale) ? '' : ' in '.$locale);
        });
    }));

    $exception = new AppException('user::message.test', ['name' => 'John']);
    expect($exception->getUserMessage())->toBe('user.test with {"name":"John"}');

    $exceptionWithLocale = new AppException('user::message.test', [], 'id');
    expect($exceptionWithLocale->getUserMessage())->toBe('user.test in id');
});

test('returns correct log message', function () {
    $exception = new AppException('user::message.log', logMessage: 'Specific log detail.');
    expect($exception->getLogMessage())->toBe('Specific log detail.');

    $exceptionDefault = new AppException('user::message.default');
    expect($exceptionDefault->getLogMessage())->toBe('user::message.default');
});

test('returns correct context', function () {
    $context = ['model_id' => 1, 'action' => 'delete'];
    $exception = new AppException('user::message.context', context: $context);
    expect($exception->getContext())->toBe($context);
});

test('returns limited sub trace', function () {
    $exception = new AppException('Test');
    $trace = $exception->getSubTrace();
    expect(count($trace))->toBeLessThanOrEqual(6); // Max 6 stacks
    expect($trace)->toBeArray();
});

test('includes correct data in report context', function () {
    $userMessage = 'user::message.report';
    $logMessage = 'Detailed error for reporting.';
    $context = ['user_id' => 5];
    $exception = new AppException(
        userMessage: $userMessage,
        logMessage: $logMessage,
        context: $context
    );

    // Mock the translator for getUserMessage
    /** @phpstan-ignore-next-line */
    $this->app->instance('translator', Mockery::mock(\Illuminate\Translation\Translator::class, function ($mock) {
        $mock->shouldReceive('getFromJson')->andReturnUsing(function ($key) {
            return str_replace('user::message.', '', $key);
        });
        $mock->shouldReceive('get')->andReturnUsing(function ($key) {
            return str_replace('user::message.', '', $key);
        });
    }));

    $reportContext = $exception->context();

    expect($reportContext)->toBeArray()
        ->and($reportContext)->toHaveKeys(['user_message', 'log_message', 'user_id'])
        ->and($reportContext['user_message'])->toBe('report')
        ->and($reportContext['log_message'])->toBe($logMessage)
        ->and($reportContext['user_id'])->toBe(5);
});

test('reports the exception correctly', function () {
    Log::spy(); // Use Log spy to assert logging

    $exception = new AppException('user::message.report', logMessage: 'Log for report test.', context: ['key' => 'value']);

    // Mock the translator for getUserMessage within the report method
    /** @phpstan-ignore-next-line */
    $this->app->instance('translator', Mockery::mock(\Illuminate\Translation\Translator::class, function ($mock) {
        $mock->shouldReceive('getFromJson')->andReturnUsing(function ($key) {
            return str_replace('user::message.', '', $key);
        });
        $mock->shouldReceive('get')->andReturnUsing(function ($key) {
            return str_replace('user::message.', '', $key);
        });
    }));

    $exception->report();

    Log::shouldHaveReceived('error')
        ->once()
        ->with('Log for report test.', Mockery::subset([
            'user_message' => 'report',
            'context' => ['key' => 'value'],
            'exception_trace' => Mockery::type('array'), // Check for array, content is dynamic
        ]));
});

test('renders a JSON response for API requests', function () {
    $exception = new AppException('API Error', code: 401);
    $request = Request::create('/api/test', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);

    $response = $exception->render($request);

    expect($response)->toBeInstanceOf(JsonResponse::class)
        ->and($response->getStatusCode())->toBe(401);

    $content = json_decode($response->getContent(), true);
    expect($content)->toBeArray()
        ->and($content)->toHaveKey('message')
        ->and($content['message'])->toBe('API Error');

    // Test debug mode
    config()->set('app.debug', true);
    $responseDebug = $exception->render($request);
    $contentDebug = json_decode($responseDebug->getContent(), true);
    expect($contentDebug)->toHaveKeys(['message', 'context', 'stack']);
    expect($contentDebug['context'])->toBeArray()->and($contentDebug['stack'])->toBeArray();
    config()->set('app.debug', false); // Reset
});

test('renders a redirect response for web requests', function () {
    $exception = new AppException('Web Error Message');
    $request = Request::create('/web/test', 'GET');

    $response = $exception->render($request);

    expect($response)->toBeInstanceOf(RedirectResponse::class)
        ->and($response->getSession()->get('error'))->toBe('Web Error Message')
        ->and($response->getTargetUrl())->toBe(url()->previous());
});
