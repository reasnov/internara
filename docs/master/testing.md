# Internara - Testing Guide

This document outlines the philosophy, conventions, and workflow for writing tests in this project. Code quality and stability are top priorities, and testing is a cornerstone of our development process.

## Table of Contents

-   [Internara - Testing Guide](#internara---testing-guide)
    -   [Table of Contents](#table-of-contents)
    -   [Testing Philosophy](#testing-philosophy)
    -   [Core Framework: Pest](#core-framework-pest)
    -   [Test Directory Structure](#test-directory-structure)
    -   [Writing Tests: The Pest Way](#writing-tests-the-pest-way)
        -   [Basic Structure](#basic-structure)
        -   [Accessing Laravel Helpers with `uses()`](#accessing-laravel-helpers-with-uses)
        -   [Feature Test Example](#feature-test-example)
        -   [Unit Test Example](#unit-test-example)
    -   [Creating \& Running Tests](#creating--running-tests)
        -   [1. Application-Level Tests](#1-application-level-tests)
            -   [Creating Tests](#creating-tests)
        -   [2. Module-Level Tests](#2-module-level-tests)
            -   [Creating Module Tests](#creating-module-tests)
        -   [Running Tests](#running-tests)
    -   [Fakes \& Mocks](#fakes--mocks)
    -   [Appendix: Class-Based Tests (PHPUnit Style)](#appendix-class-based-tests-phpunit-style)
        -   [Application Feature Test Example](#application-feature-test-example)
        -   [Application Unit Test Example](#application-unit-test-example)
        -   [Module Feature Test Example](#module-feature-test-example)

## Testing Philosophy

-   **Test First (When Practical)**: Consider writing tests before or alongside new feature code. This TDD-lite approach helps design better, more testable code.
-   **Comprehensive Coverage**: Every new feature or bug fix must be accompanied by relevant tests to verify its functionality and prevent future regressions.
-   **Don't Delete Tests**: Existing tests should not be removed. If functionality changes, update the corresponding tests to reflect the new behavior.

## Core Framework: Pest

This project uses [Pest](https://pestphp.com/) as its primary testing framework. Pest is built on top of PHPUnit but provides a more expressive and readable syntax, allowing us to write clean, elegant tests.

## Test Directory Structure

The project has two primary locations for tests: the main `/tests` directory and a `tests` directory within each module. The `phpunit.xml` configuration is already set up to discover tests from all these locations automatically.

-   `/tests/Feature`: For application-level Feature Tests.
-   `/tests/Unit`: For application-level Unit Tests.
-   `modules/{ModuleName}/tests/Feature`: For a module's specific Feature Tests.
-   `modules/{ModuleName}/tests/Unit`: For a module's specific Unit Tests.

## Writing Tests: The Pest Way

### Basic Structure

Each test is a closure passed to a global `test()` or `it()` function. A good test follows the **Arrange, Act, Assert** pattern.

```php
test('a human-readable description of what this test does', function () {
    // 1. Arrange: Set up initial conditions
    // 2. Act: Execute the code to be tested
    // 3. Assert: Verify the outcome
});
```

### Accessing Laravel Helpers with `uses()`

For any test that needs to interact with the Laravel framework (like Feature tests), you must include `uses(Tests\TestCase::class);` at the top of your file. This function imports all of Laravel's helper methods into your test, allowing you to use `$this->get()`, `$this->actingAs()`, etc., within a clean, functional test.

This is the standard and preferred way to write tests for this project.

```php
<?php

// Import the base TestCase to get all Laravel testing helpers
uses(Tests\TestCase::class);

test('can access a protected route when authenticated', function() {
    // The 'authenticate' method would be a custom helper in your TestCase
    $this->authenticate();

    // Now you can use framework helpers like get() and assertOk()
    $this->get(route('app.dashboard'))->assertOk();
});
```

### Feature Test Example

This example demonstrates a complete Feature test for a module, using the `uses()` helper to enable framework functionality.

```php
<?php
// modules/User/tests/Feature/AuthenticationTest.php

use Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

// This line is crucial for feature tests
uses(Tests\TestCase::class, RefreshDatabase::class);

test('a user can log in with correct credentials', function () {
    // Arrange: Create a user
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    // Act: Attempt to log in
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    // Assert: Ensure the user is authenticated and redirected
    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});
```

### Unit Test Example

For simple unit tests that don't need the Laravel framework, the `uses()` statement is not required.

```php
<?php
// tests/Unit/StringHelperTest.php

test('the helper function correctly formats a string', function () {
    // Arrange
    $input = 'hello world';

    // Act
    $result = str_title($input);

    // Assert
    expect($result)->toBe('Hello World');
});
```

## Creating & Running Tests

### 1. Application-Level Tests

Use these commands for tests related to the main application, located in `/tests`.

#### Creating Tests

```bash
# Create a new Feature Test
php artisan make:test --pest UserRegistrationTest

# Create a new Unit Test
php artisan make:test --pest StringHelperTest --unit
```

### 2. Module-Level Tests

Use these commands for tests related to a specific module, located in `modules/{ModuleName}/tests`.

#### Creating Module Tests

```bash
# Create a new Feature test for the "User" module
php artisan module:make-test UserCanLoginTest User --feature

# Create a new Unit test for the "User" module
php artisan module:make-test UserDataTest User
```

### Running Tests

Use the `test` Artisan command.

```bash
# Run all tests in the project (app and modules)
php artisan test

# Run tests in parallel to speed up execution (highly recommended)
php artisan test --parallel

# Run all tests for a specific module (e.g., User)
php artisan test --filter=User

# Run a single, specific test file
php artisan test tests/Feature/UserRegistrationTest.php

# Run tests whose names contain "homepage"
php artisan test --filter=homepage
```

## Fakes & Mocks

When testing, we often want to isolate our code from external services. Laravel's `fake()` methods are incredibly useful for this.

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderShipped;

uses(Tests\TestCase::class);

test('a shipping email is sent when an order is completed', function () {
    // Arrange: Use the Mail Fake
    Mail::fake();
    $order = createOrder(); // A helper function to create an order

    // Act: Run the order shipping logic
    $this->shipOrder($order);

    // Assert: Confirm that an OrderShipped mailable was sent
    Mail::assertSent(OrderShipped::class);
});
```

## Appendix: Class-Based Tests (PHPUnit Style)

While this project prefers the functional style of Pest, the traditional class-based testing approach is fully supported. This can be useful for grouping many related tests in one class or for using `setUp()` / `tearDown()` methods for complex test arrangements.

### Application Feature Test Example

```php
<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleFeatureTest extends TestCase
{
    use RefreshDatabase;

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
namespace Tests\Unit;
use PHPUnit\Framework\TestCase;

class ExampleUnitTest extends TestCase
{
    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }
}
```

### Module Feature Test Example

The code inside a module test is identical to an application test. The only difference is its **location and namespace**. This example would be located at `modules/User/tests/Feature/UserDashboardTest.php`.

```php
<?php
namespace Modules\User\Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_authenticated_user_can_view_their_dashboard(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/user/dashboard');

        $response->assertOk();
        $response->assertSee("Welcome, {$user->name}");
    }
}
```
