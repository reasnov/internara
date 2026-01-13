# Spatie Laravel Model Status Integration

This document outlines the integration and usage of the `spatie/laravel-model-status` package within the Internara project. This package provides an elegant and flexible way to add a status to any Eloquent model, allowing for clear and standardized management of an entity's lifecycle.

## 1. Introduction

The `spatie/laravel-model-status` package is a robust solution for attaching a status to any Eloquent model. Instead of relying on enum fields or custom tables for each status type, this package centralizes status management, making it easy to track and query the state of your models. It is particularly useful for workflows where models transition through various defined states, such as user verification, internship application stages, or payment processing.

## 2. Installation (Conceptual)

The package can be installed via Composer. Once installed, its service provider is typically auto-discovered by Laravel.
```bash
composer require spatie/laravel-model-status
```
After installation, you'll need to publish and run the migrations to create the `statuses` table, which stores the history of statuses for your models.

## 3. Usage

### 3.1 Applying the Trait

To enable status management for an Eloquent model, use the `HasStatuses` trait:

```php
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStatus\HasStatuses;

class MyModel extends Model
{
    use HasStatuses;

    // ...
}
```

### 3.2 Setting and Retrieving Statuses

You can set the status of a model instance using the `setStatus` method:

```php
$myModel = MyModel::find(1);

// Set a new status
$myModel->setStatus('pending');

// Set a status with a reason
$myModel->setStatus('rejected', 'User provided invalid documents');
```

To retrieve the current status:

```php
$myModel->currentStatus; // 'pending'
$myModel->status(); // Returns a Status object
```

You can also retrieve the status history:

```php
$myModel->statuses; // Collection of Status objects
```

### 3.3 Defining Allowed Statuses and Transitions

It's good practice to define the allowed statuses and transitions for a model. This can be done by overriding the `getAllowedStatuses` and `getStatusTransitions` methods in your model:

```php
class MyModel extends Model
{
    use HasStatuses;

    public function getAllowedStatuses(): array
    {
        return ['pending', 'approved', 'rejected', 'archived'];
    }

    public function getStatusTransitions(): array
    {
        return [
            'pending' => ['approved', 'rejected'],
            'approved' => ['archived'],
            'rejected' => ['archived'],
        ];
    }
}
```

Then, you can transition statuses safely:

```php
// Will only succeed if 'approved' is an allowed transition from 'pending'
$myModel->transitionTo('approved');
```

### 3.4 Querying Models by Status

The `HasStatuses` trait adds convenient query scopes:

```php
// Retrieve all models with a specific status
MyModel::whereStatus('pending')->get();

// Retrieve all models *not* having a specific status
MyModel::whereNotStatus('archived')->get();

// Retrieve all models with any of the given statuses
MyModel::whereStatusIn(['pending', 'rejected'])->get();
```

## 4. Configuration

The package's configuration file (`config/model-status.php`) can be published to customize behavior, such as:

*   The table name for statuses.
*   The model used for statuses (if you extend the default `Status` model).

## 5. Internara Specifics

Within Internara, `spatie/laravel-model-status` will be utilized to standardize status management across various domain models, including but not limited to:

*   **User Statuses:** Managing the lifecycle of user accounts (e.g., `active`, `suspended`, `pending_verification`).
*   **Internship Application Statuses:** Tracking the progress of internship applications (e.g., `submitted`, `under_review`, `accepted`, `rejected`).
*   **Any other entity** requiring a clear, auditable status workflow.

This consistent approach ensures maintainability, simplifies querying, and provides a clear history of state changes for critical application data.