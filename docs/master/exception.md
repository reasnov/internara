# Internara - Exception Handling Guidelines

This document outlines the philosophy, conventions, and best practices for handling exceptions within the Internara application. Proper exception handling is crucial for maintaining application stability, providing meaningful feedback to users, and facilitating efficient debugging.

---

**Table of Contents**

1.  [Philosophy of Exception Handling](#1-philosophy-of-exception-handling)
2.  [Key Exception Classes](#2-key-exception-classes)
    *   [2.1 `AppException` (`Modules\Shared\Exceptions\AppException`)](#21-appexception-modulessharedexceptionsappexception)
    *   [2.2 `RecordNotFoundException` (`Modules\Shared\Exceptions\RecordNotFoundException`)](#22-recordnotfoundexception-modulessharedexceptionsrecordnotfoundexception)
    *   [2.3 Laravel Built-in Exceptions](#23-laravel-built-in-exceptions)
3.  [When and How to Throw Exceptions](#3-when-and-how-to-throw-exceptions)
    *   [3.1 Throwing `AppException`](#31-throwing-appexception)
    *   [3.2 Throwing `RecordNotFoundException`](#32-throwing-recordnotfoundexception)
    *   [3.3 Catching and Re-throwing Domain-Specific Exceptions](#33-catching-and-re-throwing-domain-specific-exceptions)
4.  [Global Exception Handling Strategy](#4-global-exception-handling-strategy)
    *   [4.1 User-Friendly Feedback](#41-user-friendly-feedback)
    *   [4.2 Internal Logging](#42-internal-logging)

---

## 1. Philosophy of Exception Handling

Our approach to exception handling is guided by two core principles:

*   **Clarity and Specificity:** Always throw the most specific exception that accurately describes the problem. Avoid generic `\Exception` unless absolutely necessary as a last resort. This makes debugging easier and allows for more granular error handling in consuming code.
*   **User vs. Internal:** Clearly distinguish between messages intended for the end-user (user-friendly, non-technical) and messages intended for developers/logging (technical details, stack traces). Users should never see technical jargon or sensitive system information.

## 2. Key Exception Classes

Internara leverages both custom domain-specific exceptions and Laravel's built-in exceptions to manage errors effectively.

### 2.1 `AppException` (`Modules\Shared\Exceptions\AppException`)

This is the foundational custom exception class for all domain-specific or business logic errors within the application. It provides a robust mechanism to separate user-facing messages from internal logging details.

*   **Purpose:** To encapsulate business logic violations, validation failures (not handled by Laravel's `ValidationException`), or other application-specific issues, offering a consistent way to communicate these problems.
*   **Key Features:**
    *   **`$userMessage`**: The safe, non-technical message to display to the end-user.
    *   **`$logMessage`**: An optional, more technical message intended for internal logging.
    *   **`$context`**: An array of contextual data passed directly via the constructor for structured logging.
    *   **`$code`**: An HTTP status code (default `422 Unprocessable Content`).
    *   **`render()` Method**: Automatically handles response generation. For JSON/API requests, it returns a structured JSON response including the message, context, and a partial stack trace. For web requests, it redirects the user back with the error message.
*   **Constructor:**
    ```php
    public function __construct(
        protected string $userMessage,
        protected ?string $logMessage = null,
        protected int $code = 422,
        protected ?Throwable $previous = null,
        protected array $context = []
    )
    ```

### 2.2 `RecordNotFoundException` (`Modules\Shared\Exceptions\RecordNotFoundException`)

A specialized exception designed for scenarios where a requested data record or resource cannot be located in the system. It extends `AppException`.

*   **Purpose:** To standardize error reporting when `find(id)` operations return `null`, or `findOrFail(id)` methods fail to retrieve a resource from the Repository or Service layer.
*   **Key Features:**
    *   Defaults to an HTTP status code of **`404 Not Found`**.
    *   Accepts an `Entity` or `array` of record identifiers, which are passed as **structured context** to `AppException` for enhanced logging.
*   **Usage Context:** Primarily thrown by Repository `findOrFail` methods or Service methods when a dependency on an existing record is not met.

### 2.3 Laravel Built-in Exceptions

Continue to utilize Laravel's native exceptions for framework-level concerns:

*   **`Illuminate\Validation\ValidationException`**: For failures of Laravel's validation rules, typically thrown by Form Requests or explicit validator calls.
*   **`Illuminate\Auth\AuthenticationException`**: Indicates that a user is not authenticated for the action they are attempting.
*   **`Illuminate\Auth\Access\AuthorizationException`**: Signifies that an authenticated user does not have the necessary permissions (via policies or gates) to perform an action.
*   **`Symfony\Component\HttpKernel\Exception\NotFoundHttpException`**: For general HTTP 404 responses, often when a route or resource is not found at the HTTP level.
*   **`Illuminate\Database\QueryException`**: Thrown when underlying database operations fail (e.g., constraint violations, invalid SQL).

## 3. When and How to Throw Exceptions

### 3.1 Throwing `AppException`

Throw `AppException` when custom business logic rules are violated, and you need to convey a specific message to the user while capturing detailed, structured logs.

```php
namespace Modules\User\Services;

use Modules\Shared\Exceptions\AppException;
use Modules\User\Entities\UserEntity;

class UserService
{
    public function activateUser(UserEntity $user): UserEntity
    {
        if ($user->status !== 'pending_activation') {
            throw new AppException(
                userMessage: 'User account cannot be activated in its current state.',
                logMessage: 'Attempted to activate a non-pending user.',
                code: 400, // Bad Request
                context: ['user_id' => $user->getKey(), 'status' => $user->status]
            );
        }
        // ... activation logic
        return $user;
    }
}
```

### 3.2 Throwing `RecordNotFoundException`

Throw `RecordNotFoundException` whenever a required resource cannot be found. Pass the identifying details as the `record` parameter for logging context.

```php
namespace Modules\Post\Services;

use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\Post\Contracts\Repositories\PostRepository;
use Modules\Post\Entities\PostEntity;

class PostService
{
    public function __construct(private readonly PostRepository $postRepository) {}

    public function getPostById(string $postId): PostEntity
    {
        // Rely on the repository's findOrFail method for clean error handling
        return $this->postRepository->findOrFail($postId);
    }

    public function updatePost(string $postId, array $data): PostEntity
    {
        // Example of manually throwing if not using findOrFail
        $post = $this->postRepository->find($postId);
        if (!$post) {
            throw new RecordNotFoundException(
                userMessage: "The post you are trying to update could not be found.",
                record: ['post_id' => $postId] // Provide context for logging
            );
        }
        // ... update logic
        return $this->postRepository->update($post->with($data));
    }
}
```

### 3.3 Catching and Re-throwing Domain-Specific Exceptions

When a lower-level, generic exception (like `QueryException`) occurs due to a violation of domain logic, it is best practice to catch it and re-throw a more domain-specific `AppException`. This maintains clear abstraction boundaries and provides a more user-friendly message.

```php
namespace Modules\User\Repositories;

use Illuminate\Database\QueryException;
use Modules\Shared\Exceptions\AppException;
use Modules\Core\Contracts\Entities\Entity;

class EloquentUserRepository implements UserRepository
{
    public function create(Entity $entity): Entity
    {
        try {
            $model = $this->source->create($entity->getRawAttributes());
            return $this->mapToEntity($model);
        } catch (QueryException $e) {
            // Catch specific database errors and re-throw as an application exception
            if ($e->getCode() === '23000') { // Example: Unique constraint violation
                throw new AppException(
                    userMessage: 'A user with the provided email or username already exists.',
                    code: 409, // Conflict
                    previous: $e,
                    context: ['email' => $entity->email, 'username' => $entity->username]
                );
            }
            // Re-throw as a generic application error if not specific
            throw new AppException(
                userMessage: 'An unexpected error occurred while creating the user.',
                logMessage: 'Database error creating user.',
                code: 500,
                previous: $e,
                context: $entity->toArray()
            );
        }
    }
}
```

## 4. Global Exception Handling Strategy

Laravel's `App\Exceptions\Handler.php` is the central hub for defining how all exceptions are rendered and logged. Our custom exceptions are designed to integrate seamlessly with this system.

### 4.1 User-Friendly Feedback

*   **For `AppException` and its children:** Thanks to the built-in `render()` method, these exceptions automatically generate the correct response.
    *   **JSON/API Requests:** A structured JSON response is returned, containing the `message`, `context`, and a partial `stack` trace for debugging.
    *   **Web Requests:** The user is redirected back to the previous page with the `userMessage` flashed to the session as an error.
*   **For Other Exceptions:** A generic, non-technical error message should be displayed to the user (e.g., "An unexpected server error occurred.") to prevent exposing sensitive system information.

### 4.2 Internal Logging

*   **For all Exceptions:** The full stack trace and the `logMessage` should be logged in detail for debugging.
*   **Contextual Logging:** The `AppException` constructor accepts a `$context` array. This data is automatically included in log entries, providing rich, structured information for debugging without polluting the log message string itself.
*   **Sensitivity:** Sensitive information (passwords, API keys) must **never** be passed into the context or log message.

This robust exception handling strategy ensures that Internara provides a professional user experience even during errors, while simultaneously offering comprehensive tools for developers to diagnose and resolve issues efficiently.