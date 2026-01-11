# Internara - Exception Handling Guidelines

This document outlines the philosophy, conventions, and best practices for handling exceptions within the Internara application. Proper exception handling is crucial for maintaining application stability, providing meaningful feedback to users, and facilitating efficient debugging.

---

## 1. Philosophy of Exception Handling

Our approach to exception handling is guided by two core principles:

*   **Clarity and Specificity:** Always throw the most specific exception that accurately describes the problem. Avoid generic `\Exception` unless absolutely necessary as a last resort. This makes debugging easier and allows for more granular error handling in consuming code.
*   **User vs. Internal:** Clearly distinguish between messages intended for the end-user (user-friendly, non-technical) and messages intended for developers/logging (technical details, stack traces). Users should never see technical jargon or sensitive system information.

---

## 2. Key Exception Classes

Internara leverages both custom domain-specific exceptions and Laravel's built-in exceptions to manage errors effectively.

### 2.1 `AppException` (`Modules\Shared\Exceptions\AppException`)

This is the foundational custom exception class for all domain-specific or business logic errors within the application. It is designed to work seamlessly with Laravel's localization system.

*   **Purpose:** To encapsulate business logic violations or other application-specific issues, offering a consistent and translatable way to communicate these problems.
*   **Key Features:**
    *   **`$userMessage`**: A **translation key** used to retrieve a localized, user-friendly message.
    *   **`$replace`**: An array of parameters for the translation.
    *   **`$locale`**: An optional specific locale for the translation.
    *   **`$logMessage`**: An optional, more technical message intended for internal logging.
    *   **`$context`**: An array of contextual data for structured logging.
    *   **`render()` Method**: Automatically translates the message and handles response generation (JSON or Redirect).
*   **Constructor:**
    ```php
    public function __construct(
        string $userMessage,
        protected array $replace = [],
        protected ?string $locale = null,
        ?string $logMessage = null,
        int $code = 422,
        ?Throwable $previous = null,
        protected array $context = []
    )
    ```

### 2.2 `RecordNotFoundException` (`Modules\Shared\Exceptions\RecordNotFoundException`)

A specialized exception for when a requested data record cannot be located. It extends `AppException` and uses a default localized message.

*   **Purpose:** To standardize error reporting for missing records.
*   **Key Features:**
    *   Defaults to an HTTP status code of **`404 Not Found`**.
    *   By default, uses the translation key `shared::exceptions.record_not_found` but can be overridden.
    *   Accepts `replace` parameters for dynamic messages and `record` for logging context.
*   **Constructor:**
    ```php
    public function __construct(
        string $userMessage = 'shared::exceptions.record_not_found',
        array $replace = [],
        ?string $locale = null,
        array $record = [],
        int $code = 404,
        ?Throwable $previous = null
    )
    ```

### 2.3 Laravel Built-in Exceptions

Continue to utilize Laravel's native exceptions for framework-level concerns like `ValidationException`, `AuthenticationException`, `AuthorizationException`, etc.

---

## 3. Localization of User Messages

All user-facing error messages thrown via `AppException` **must** be localized using translation keys. This ensures our application can support multiple languages and that messages are managed centrally.

### 3.1. Three-Tiered Language File Structure

We organize our exception messages into a three-tiered structure to maintain separation of concerns:

1.  **Shared Errors**: For generic, system-wide errors not tied to a specific module.
    - **Location**: `modules/Shared/lang/{locale}/exceptions.php`
    - **Example**: General "record not found" or "server error" messages.

2.  **Core Errors**: For errors related to core business logic that may span multiple modules.
    - **Location**: `modules/Core/lang/{locale}/exceptions.php`
    - **Example**: A business rule violation related to the overall internship program.

3.  **Module-Specific Errors**: For errors that are specific to a single module's domain logic. This is the most common use case.
    - **Location**: `modules/{ModuleName}/lang/{locale}/exceptions.php`
    - **Example**: An error specific to user creation in the `User` module.

### 3.2. Namespaced Translation Keys

To call a translation from a specific module, we use **namespaced keys**.

- **Format**: `{module_name}::exceptions.{key_name}`
- **Examples**:
  - `shared::exceptions.record_not_found`
  - `user::exceptions.owner_cannot_be_deleted`

### 3.3. How to Throw a Localized `AppException`

This example shows the complete flow for throwing an exception for the "owner cannot be deleted" rule in `UserService`.

**1. Throw the Exception with a Namespaced Key**

The `$userMessage` is now a translation key.

**File: `modules/User/src/Services/UserService.php`**
```php
// ...
use Modules\Shared\Exceptions\AppException;

class UserService
{
    public function delete(string $id): bool
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('owner')) {
            throw new AppException(
                userMessage: 'user::exceptions.owner_cannot_be_deleted',
                code: 403
            );
        }

        return $user->delete();
    }
}
```

**3. Define the Translations in Language Files**

Create the corresponding language files inside the `User` module.

**File: `modules/User/lang/en/exceptions.php`**
```php
<?php

return [
    'owner_cannot_be_deleted' => 'The owner account cannot be deleted.',
];
```

**File: `modules/User/lang/id/exceptions.php`**
```php
<?php

return [
    'owner_cannot_be_deleted' => 'Akun owner tidak dapat dihapus.',
];
```

**3. Example with Replacement Parameters**

To pass dynamic data to your translated message, use the `$replace` parameter.

**Throwing the exception:**
```php
throw new AppException(
    userMessage: 'shared::exceptions.record_not_found_by_id',
    replace: ['id' => $userId],
    code: 404
);
```

**Language file (`modules/Shared/lang/en/exceptions.php`):**
```php
<?php

return [
    'record_not_found_by_id' => 'The record with ID :id could not be found.',
];
```

---

**4. Example Throwing `RecordNotFoundException`**

You can throw `RecordNotFoundException` directly. By default, it uses `shared::exceptions.record_not_found`. You can also pass replacement parameters or a specific translation key.

```php
throw new \Modules\Shared\Exceptions\RecordNotFoundException(
    replace: ['id' => $userId],
    record: ['id' => $userId], // For logging context
    code: 404
);

// Or with a custom message key and parameters
throw new \Modules\Shared\Exceptions\RecordNotFoundException(
    userMessage: 'user::exceptions.user_not_found',
    replace: ['userId' => $userId],
    record: ['id' => $userId],
    code: 404
);
```

You would need to define the `record_not_found` key in `modules/Shared/lang/{locale}/exceptions.php` and `user_not_found` in `modules/User/lang/{locale}/exceptions.php` respectively.

---

## 4. Global Exception Handling Strategy

Laravel's `App\Exceptions\Handler.php` is the central hub for defining how all exceptions are rendered and logged. Our custom exceptions are designed to integrate seamlessly with this system.

### 4.1 User-Friendly Feedback

*   **For `AppException`:** The built-in `render()` method automatically resolves the translation key from `$userMessage` and generates the correct response.
    *   **JSON/API Requests:** A JSON response is returned with the translated `message`.
    *   **Web Requests:** The user is redirected back with the translated error message flashed to the session.
*   **For Other Exceptions:** A generic, non-technical error message should be displayed to the user.

### 4.2 Internal Logging

*   **For all Exceptions:** The full stack trace and the `logMessage` should be logged in detail for debugging.
*   **Contextual Logging:** The `AppException` constructor accepts a `$context` array. This data is automatically included in log entries, providing rich, structured information.
*   **Sensitivity:** Sensitive information (passwords, API keys) must **never** be passed into the context or log message.