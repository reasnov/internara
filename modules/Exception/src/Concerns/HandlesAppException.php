<?php

declare(strict_types=1);

namespace Modules\Exception\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Exception\AppException;
use Throwable;

/**
 * Trait HandlesAppException.
 *
 * Provides a standardized way to interact with and handle `AppException` instances
 * across the application, particularly in contexts like controllers or services.
 * This trait centralizes common logic related to application-specific exceptions.
 */
trait HandlesAppException
{
    /**
     * Determines if the given Throwable instance is an AppException.
     *
     * @param Throwable $exception The exception to check.
     *
     * @return bool True if the exception is an instance of AppException, false otherwise.
     */
    protected function isAppException(Throwable $exception): bool
    {
        return $exception instanceof AppException;
    }

    /**
     * Creates and returns a new AppException instance.
     *
     * This is a convenient helper method to construct an AppException with
     * default parameters or specific overrides.
     *
     * @param string $userMessage The user-friendly message key or literal message.
     * @param array $replace Replacements for the user message translation.
     * @param string|null $locale Specific locale for the user message.
     * @param string|null $logMessage The technical log message.
     * @param int $code The HTTP status code or internal error code.
     * @param Throwable|null $previous The previous exception in the chain.
     * @param array $context Additional context data for logging.
     *
     * @return AppException A new instance of AppException.
     */
    protected function newAppException(
        string $userMessage,
        array $replace = [],
        ?string $locale = null,
        ?string $logMessage = null,
        int $code = 422,
        ?Throwable $previous = null,
        array $context = []
    ): AppException {
        return new AppException(
            userMessage: $userMessage,
            replace: $replace,
            locale: $locale,
            logMessage: $logMessage,
            code: $code,
            previous: $previous,
            context: $context
        );
    }

    /**
     * Throws a new AppException instance.
     *
     * This method acts as a convenience wrapper for immediately throwing
     * an AppException after its creation.
     *
     * @param string $userMessage The user-friendly message key or literal message.
     * @param array $replace Replacements for the user message translation.
     * @param string|null $locale Specific locale for the user message.
     * @param string|null $logMessage The technical log message.
     * @param int $code The HTTP status code or internal error code.
     * @param Throwable|null $previous The previous exception in the chain.
     * @param array $context Additional context data for logging.
     *
     * @throws AppException
     */
    protected function throwAppException(
        string $userMessage,
        array $replace = [],
        ?string $locale = null,
        ?string $logMessage = null,
        int $code = 422,
        ?Throwable $previous = null,
        array $context = []
    ): void {
        throw $this->newAppException(
            userMessage: $userMessage,
            replace: $replace,
            locale: $locale,
            logMessage: $logMessage,
            code: $code,
            previous: $previous,
            context: $context
        );
    }

    /**
     * Reports any Throwable instance using Laravel's reporting mechanism.
     *
     * If the Throwable is an AppException, its custom reporting logic is used.
     * Otherwise, it's reported as a generic exception.
     *
     * @param Throwable $exception The exception instance to report.
     */
    protected function reportException(Throwable $exception): void
    {
        report($exception);
    }

    /**
     * Renders an AppException instance into an HTTP response.
     *
     * This method explicitly calls the render method of the AppException,
     * allowing a consuming class to control when the response is generated.
     *
     * @param AppException $exception The AppException instance to render.
     * @param Request $request The current HTTP request.
     */
    protected function renderAppException(AppException $exception, Request $request): JsonResponse|RedirectResponse
    {
        return $exception->render($request);
    }

    /**
     * Handles an exception within a Livewire component context.
     *
     * This method reports the exception and returns the event data to be dispatched.
     * The Livewire component consuming this trait should handle the actual dispatching
     * of the event and the `stopPropagation` logic.
     *
     * @param Throwable $exception The exception to handle.
     *
     * @return array{event: string, message: string} An array containing the event name and message to be dispatched.
     */
    protected function handleAppExceptionInLivewire(Throwable $exception): array
    {
        $this->reportException($exception);

        if ($this->isAppException($exception)) {
            return ['event' => 'error', 'message' => $exception->getUserMessage()];
        }

        return ['event' => 'error', 'message' => __('An unexpected error occurred.')];
    }

    /**
     * Handles an exception comprehensively, adapting the response based on the request context.
     *
     * This method serves as a central entry point for managing exceptions, ensuring
     * consistent reporting and appropriate responses for API, web, and Livewire requests.
     *
     * @param Throwable $exception The exception to handle.
     * @param Request $request The current HTTP request.
     */
    protected function handleAppException(Throwable $exception, Request $request): JsonResponse|RedirectResponse|array|null
    {
        $this->reportException($exception);

        // If it's a Livewire request, handle it specifically
        if ($request->isLivewire()) {
            return $this->handleAppExceptionInLivewire($exception);
        }

        // If it's an AppException, let its render method handle it first
        if ($this->isAppException($exception)) {
            return $this->renderAppException($exception, $request);
        }

        // For generic Throwables:
        // If the request expects JSON (API or AJAX)
        if ($request->expectsJson()) {
            return response()->json([
                'message' => config('app.debug') ? $exception->getMessage() : __('An unexpected error occurred.'),
            ], method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);
        }

        // For regular web requests, redirect back or show a generic error
        return redirect()
            ->back()
            ->withInput($request->input())
            ->with('error', config('app.debug') ? $exception->getMessage() : __('An unexpected error occurred.'));
    }
}
