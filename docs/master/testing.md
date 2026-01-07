# Internara - Testing Guide

This document outlines the philosophy, conventions, and workflow for writing tests within the Internara project. High code quality and stability are paramount, and a robust testing strategy is fundamental to achieving these goals.

## Table of Contents

-   [Internara - Testing Guide](#internara---testing-guide)
    -   [Table of Contents](#table-of-contents)
    -   [Testing Philosophy](#testing-philosophy)
        -   [Unit Tests vs. Feature Tests: Clear Responsibilities](#unit-tests-vs-feature-tests-clear-responsibilities)
    -   [Core Framework: Pest](#core-framework-pest)
    -   [Test Directory Structure](#test-directory-structure)
    -   [Writing Tests with Pest](#writing-tests-with-pest)
        -   [Basic Test Structure](#basic-test-structure)
        -   [Global Test Configuration (`tests/Pest.php`)](#global-test-configuration-testspestphp)
        -   [Feature Tests](#feature-tests)
        -   [Unit Tests](#unit-tests)
    -   [Creating \& Running Tests](#creating--running-tests)
        -   [Creating Application-Level Tests](#creating-application-level-tests)
        -   [Creating Module-Level Tests](#creating-module-level-tests)
        -   [Running Tests](#running-tests)
    -   [Fakes \& Mocks](#fakes--mocks)
    -   [Appendix: Class-Based PHPUnit Style Tests](#appendix-class-based-phpunit-style-tests)
        -   [Application Feature Test Example](#application-feature-test-example)
        -   [Application Unit Test Example](#application-unit-test-example)
        -   [Module Feature Test Example](#module-feature-test-example)

## Testing Philosophy

-   **Test-Driven Development (TDD) principles**: Embrace writing tests before or alongside new feature development. This approach fosters better design, more maintainable code, and clearer requirements.
-   **Comprehensive Coverage**: Ensure every new feature, bug fix, or significant modification is accompanied by relevant tests. This practice verifies functionality and prevents regressions.
-   **Maintain Test Suites**: Treat tests as first-class citizens. Avoid deleting existing tests; instead, update them to reflect changes in functionality.

### Unit Tests vs. Feature Tests: Clear Responsibilities

Understanding the distinct roles of Unit and Feature tests is crucial for an effective testing strategy.

-   **Unit Tests**:

    -   **Focus**: Isolated components (e.g., a single function, method, or small class).
    -   **Goal**: Verify that each unit of code performs its specific task correctly in isolation.
    -   **Characteristics**: Fast execution, mock external dependencies (database, HTTP requests, other services) to ensure only the unit under test is evaluated. They do not interact with the application's infrastructure.
    -   **When to use**: For testing algorithms, helper functions, model logic (without database interaction), service methods (with mocked dependencies).

-   **Feature Tests**:
    -   **Focus**: Integration of multiple components, covering an entire feature or user story.
    -   **Goal**: Verify that different parts of the application (e.g., controller, service, model, database, HTTP middleware) work together as expected to deliver a specific feature.
    -   **Characteristics**: Slower execution compared to unit tests. They often interact with the database, simulate HTTP requests, and leverage the full Laravel application bootstrap.
    -   **When to use**: For testing API endpoints, form submissions, user authentication flows, complex business processes involving multiple layers, and overall feature completeness.

By maintaining this clear separation, we ensure that our codebase is both thoroughly tested at a granular level and that its integrated features function correctly end-to-end.

## Core Framework: Pest

Internara utilizes [Pest](https://pestphp.com/) as its primary testing framework. Built atop PHPUnit, Pest provides a highly expressive and developer-friendly syntax, enabling the creation of clean, elegant, and readable tests.

## Test Directory Structure

Tests are organized into two main locations: the root `/tests` directory for application-wide tests and a dedicated `tests` directory within each module. The project's `phpunit.xml` is pre-configured to automatically discover tests from all these locations.

**Crucially, test files should always be placed in a dedicated subdirectory within `Feature/` or `Unit/`, reflecting the layer they are testing.** For example, `Unit/Models`, `Feature/Services`, `Feature/Http/Controllers`, or `Feature/Policies`. **Avoid placing test files directly under `Feature/` or `Unit/`.**

-   `/tests/Feature/{Layer}/{SubDir}`: For application-level Feature Tests (e.g., `tests/Feature/Http/Controllers`).
-   `/tests/Unit/{Layer}/{SubDir}`: For application-level Unit Tests (e.g., `tests/Unit/Models`).
-   `modules/{ModuleName}/tests/Feature/{Layer}/{SubDir}`: For module-specific Feature Tests (e.g., `modules/User/tests/Feature/Auth`).
-   `modules/{ModuleName}/tests/Unit/{Layer}/{SubDir}`: For module-specific Unit Tests (e.g., `modules/User/tests/Unit/Models`).

## Writing Tests with Pest

### Basic Test Structure

Pest tests are defined as closures passed to a global `test()` or `it()` function. Adhering to the **Arrange, Act, Assert** pattern enhances test clarity and maintainability.

```php
test('it should correctly process a valid input', function () {
    // 1. Arrange: Set up the test environment and initial conditions (e.g., create models, mock dependencies).
    $input = 'some value';

    // 2. Act: Execute the code or method under test.
    $result = someFunction($input);

    // 3. Assert: Verify the outcome (e.g., assert a return value, database state, or method call).
    expect($result)->toBe('expected value');
});
```

### Global Test Configuration (`tests/Pest.php`)

The `tests/Pest.php` file centrally configures the testing environment. It extends `Tests\TestCase` to bind it to your test functions and specifies which directories automatically load this configuration. This ensures a consistent, bootstrapped Laravel environment for tests that require framework interaction.

```php
<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->in(
        'Feature',
        'Unit',
        '../modules/*/tests/Feature',
        '../modules/*/tests/Unit'
    );

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
```

### Feature Tests

Feature tests focus on how different components of your application interact, testing a broader scope, typically including HTTP requests and database interactions. For tests requiring database resets, the `RefreshDatabase` trait is commonly used.

```php
<?php

// modules/User/tests/Feature/Auth/LoginTest.php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

uses(RefreshDatabase::class);

test('a user can log in with correct credentials', function () {
    // Arrange: Create a user in the database
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    // Act: Simulate a POST request to the login endpoint
    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    // Assert: Verify the user is redirected to the dashboard and authenticated
    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});
```

### Asserting Exceptions

When testing code that is expected to throw exceptions under certain conditions, Pest provides the `toThrow` expectation to gracefully assert these scenarios. This is crucial for ensuring that error handling mechanisms work as intended.

To assert an exception, wrap the code that is expected to throw an exception within a closure, and then use `expect()` with `toThrow()`. You can specify both the expected exception class and, optionally, the exact message of the exception.

```php
use Modules\Shared\Exceptions\AppException;
use InvalidArgumentException; // Example of a generic exception

// Assume a hypothetical service method that might throw an AppException
class MyService
{
    public function performAction(bool $shouldFail, string $customLogMessage = null): string
    {
        if ($shouldFail) {
            // Simulate throwing an AppException
            // If customLogMessage is provided, it will be the message returned by getMessage()
            // Otherwise, userMessage (after translation) will be the message returned by getMessage() if no logMessage is explicitly set.
            throw new AppException(
                userMessage: 'shared::exceptions.action_failed',
                logMessage: $customLogMessage, // Will be the default message for getMessage()
                code: 400
            );
        }
        return "Action performed successfully.";
    }

    public function processGenericInput(int $value): string
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Input value cannot be negative.');
        }
        return "Processed: {$value}";
    }
}

test('it throws an InvalidArgumentException for negative input', function () {
    $service = new MyService();

    // Act & Assert for a generic exception:
    expect(fn () => $service->processGenericInput(-5))
        ->toThrow(InvalidArgumentException::class, 'Input value cannot be negative.');
});

test('it throws an AppException with a specific log message', function () {
    $service = new MyService();
    $expectedLogMessage = 'Specific log detail for failure.';

    // Act & Assert for AppException with logMessage:
    // When logMessage is provided in AppException, it becomes the default message
    // returned by $exception->getMessage().
    expect(fn () => $service->performAction(true, $expectedLogMessage))
        ->toThrow(AppException::class, $expectedLogMessage);
});

test('it throws an AppException and getMessage() defaults to userMessage if logMessage is empty', function () {
    $service = new MyService();
    // Assuming 'shared::exceptions.action_failed' translates to 'The action could not be performed.'
    // For this test to be accurate, ensure 'shared::exceptions.action_failed' exists and is translated.
    $expectedUserMessage = __('shared::exceptions.action_failed');

    // Act & Assert for AppException when logMessage is null/empty:
    // If logMessage is null, the AppException constructor will use the userMessage as its default message.
    expect(fn () => $service->performAction(true, null))
        ->toThrow(AppException::class, $expectedUserMessage);
});
```

### Unit Tests

Unit tests verify small, isolated parts of your code, such as individual functions or methods, without involving the broader framework or external dependencies.

```php
<?php

// tests/Unit/Support/StringHelperTest.php

// Assuming a helper function `str_title` exists globally or is loaded.
if (! function_exists('str_title')) {
    function str_title(string $value): string
    {
        return ucwords(strtolower($value));
    }
}

test('the helper function correctly formats a string to title case', function () {
    // Arrange
    $input = 'hello world';

    // Act
    $result = str_title($input);

    // Assert
    expect($result)->toBe('Hello World');
});
```

## Creating & Running Tests

### Creating Application-Level Tests

Use these Artisan commands to generate tests for the main application, which will be stored in the `/tests` directory. Remember to specify the desired subdirectory structure.

```bash
# Create a new Feature Test (e.g., tests/Feature/Http/Controllers/UserRegistrationTest.php)
php artisan make:test Feature/Http/Controllers/UserRegistrationTest

# Create a new Unit Test (e.g., tests/Unit/Support/StringHelperTest.php)
php artisan make:test Unit/Support/StringHelperTest --unit
```

### Creating Module-Level Tests

Use the `module:make-test` Artisan command to generate tests within a specific module's `tests` directory. Ensure you include the desired subdirectory structure.

```bash
# Create a new Feature test for the "User" module (e.g., modules/User/tests/Feature/Auth/LoginTest.php)
php artisan module:make-test Auth/LoginTest User --feature

# Create a new Unit test for the "User" module (e.g., modules/User/tests/Unit/Models/UserDataTest.php)
php artisan module:make-test Models/UserDataTest User
```

### Running Tests

Execute your tests using the `artisan test` command.

```bash
# Run all tests across the entire project (application and all modules)
php artisan test

# Run tests in parallel to significantly speed up execution (highly recommended)
php artisan test --parallel

# Run all tests for a specific module (e.g., the "User" module)
php artisan test --filter=User

# Run a single, specific test file (using its full path or a pattern)
php artisan test tests/Feature/Http/Controllers/UserRegistrationTest.php

# Run tests whose names contain a specific string (e.g., "homepage")
php artisan test --filter=homepage
```

## Fakes & Mocks

Laravel provides powerful `fake()` methods and mocking capabilities to isolate your code from external services and dependencies during testing.

```php
<?php

use App\Mail\OrderShipped;
use Illuminate\Support\Facades\Mail;
use function PHPUnit\Framework\assertTrue;

// Assuming createOrder() is a helper function that creates and returns an Order model
function createOrder(): object
{
    return (object) ['id' => 1, 'customer_email' => 'customer@example.com'];
}

// Assuming shipOrder() is a function that processes an order and sends an email
function shipOrder(object $order): void
{
    Mail::to($order->customer_email)->send(new OrderShipped($order));
    assertTrue(true); // Placeholder for actual shipping logic
}

test('a shipping email is sent when an order is completed', function () {
    // Arrange: Enable Laravel's Mail Fake to prevent actual emails from being sent
    Mail::fake();
    $order = createOrder();

    // Act: Execute the order shipping logic
    shipOrder($order);

    // Assert: Verify that an OrderShipped mailable was sent
    Mail::assertSent(OrderShipped::class);
    Mail::assertSent(OrderShipped::class, function (OrderShipped $mail) use ($order) {
        return $mail->hasTo($order->customer_email);
    });
});
```

## Appendix: Class-Based PHPUnit Style Tests

While Pest's functional style is preferred, the project fully supports traditional class-based testing using PHPUnit syntax. This approach can be beneficial for grouping numerous related tests within a single class or for leveraging `setUp()` and `tearDown()` methods for complex test arrangements.

### Application Feature Test Example

```php
<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleFeatureTest extends TestCase
{
    use RefreshDatabase; // Use this trait to refresh the database before each test

    /**
     * Test that the application returns a successful response for the home page.
     *
     * @return void
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
```

### Application Unit Test Example

```php
<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase as PHPUnitTestCase; // Alias to avoid conflict with Tests\TestCase

class ExampleUnitTest extends PHPUnitTestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
```

### Module Feature Test Example

This example demonstrates a class-based feature test for a module. Its location (`modules/User/tests/Feature/Auth/UserDashboardTest.php`) and namespace are the primary distinctions from an application-level test.

```php
<?php

namespace Modules\User\Tests\Feature\Auth;

use App\Models\User; // Assuming App\Models\User exists in the main application
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase; // Use this trait to refresh the database before each test

    /** @test */
    public function test_an_authenticated_user_can_view_their_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/dashboard');

        $response->assertOk();
        $response->assertSee("Welcome, {$user->name}");
    }
}
```
