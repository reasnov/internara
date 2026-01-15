# Spatie Laravel Activitylog Integration

This document outlines the integration of the `spatie/laravel-activitylog` package within the
Internara project, specifically for implementing the **System Monitor and User Activity Log**
feature as part of the `Log` module. This package provides a robust and flexible way to log various
activities performed by users or the system.

---

## 1. Overview

The `spatie/laravel-activitylog` package offers a straightforward solution for tracking actions,
changes, and events across your application. It allows logging activities related to Eloquent
models, custom events, and provides an easy way to retrieve and display these logs.

## 2. Installation (Documented Steps)

To integrate `spatie/laravel-activitylog`, the following steps _would be_ performed:

1.  **Install via Composer:**

    ```bash
    composer require spatie/laravel-activitylog
    ```

2.  **Publish Configuration and Migration:**

    ```bash
    php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-config"
    php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
    ```

    This publishes `config/activitylog.php` and the necessary migration file(s).

3.  **Run Migrations:**
    ```bash
    php artisan migrate
    ```
    This creates the `activity_log` table in the database.

## 3. Customization and Integration within the `Log` Module

The `spatie/laravel-activitylog` package is integrated within a dedicated `Log` module to maintain
the project's modular architecture and align with Internara's conventions.

### 3.1. Custom Activity Model (`modules/Log/src/Models/Activity.php`)

To fully control and extend the activity log entries, a custom `Activity` model will be created
within `modules/Log/src/Models/Activity.php`. This model will extend the package's base `Activity`
model.

```php
<?php

namespace Modules\Log\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    // Customizations for the Activity model can be added here.
    // For example, adding custom casts, relations, or methods.

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'activity_log'; // Ensure this matches the migration table name
}
```

### 3.2. Configuration (`config/activitylog.php`)

The published configuration file `config/activitylog.php` will be adjusted to use our custom
`Activity` model and to align with other Internara logging preferences.

```php
<?php

return [
    'activity_model' => \Modules\Log\Models\Activity::class, // Use our custom model
    // ... other configurations as needed (e.g., database connection, table name)
];
```

### 3.3. Migration Adjustments (Optional)

If the project mandates UUIDs for all primary keys, the published `create_activity_log_table`
migration _may need_ to be adjusted. The default `activitylog` migration typically uses
auto-incrementing integers for its primary key. If UUIDs are required for the `activity_log` table,
the migration would be modified accordingly.

**Example of Potential Migration Adjustment for UUID Primary Key:** (This is illustrative and
depends on project-specific UUID implementation)

```php
// In a custom migration or a modified published migration
Schema::create('activity_log', function (Blueprint $table) {
    $table->uuid('id')->primary(); // Change to UUID primary key
    // ... rest of the table definition
});
```

## 4. Usage Conventions

To effectively utilize `spatie/laravel-activitylog`:

### 4.1. Logging Model Events

To log changes to Eloquent models, use the `Spatie\Activitylog\Traits\LogsActivity` trait within
your model:

```php
<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity; // Import the trait
use Spatie\Activitylog\LogOptions; // Import LogOptions

class User extends Authenticatable
{
    use HasFactory, LogsActivity; // Use the trait

    // ... other model properties ...

    /**
     * Define the options for logging activity.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Log all fillable attributes
            ->logOnlyDirty() // Only log changed attributes
            ->dontSubmitEmptyLogs(); // Don't log if no attributes changed
    }

    /**
     * Define attributes that should be logged.
     * Alternatively, use $this->logFillable() in getActivitylogOptions.
     */
    // protected static $logAttributes = ['name', 'email'];

    /**
     * Define attributes that should be ignored when logging.
     */
    // protected static $ignoreChangedAttributes = ['password', 'updated_at'];
}
```

### 4.2. Logging Custom Activities

You can log custom activities anywhere in your code:

```php
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Facades\Causer; // For setting the causer if not automatically determined

// Log a simple activity
activity('User')->log('User {$user->name} created.');

// Log an activity with a subject
activity()
    ->performedOn($user) // The Eloquent model that was interacted with
    ->causedBy(auth()->user()) // The user who performed the action
    ->withProperty('ip_address', request()->ip()) // Custom properties
    ->log('User created');

// Log an activity with a description
activity()->log('This is my custom activity. I want to log it');
```

### 4.3. Retrieving Activity Logs

You can easily retrieve activity logs:

```php
use Modules\Log\Models\Activity; // Use our custom Activity model

// Get all activities
$allActivities = Activity::all();

// Get activities for a specific user
$userActivities = Activity::causedBy($user)->get();

// Get activities for a specific model
$modelActivities = Activity::forSubject($post)->get();

// Filter by description
$loginActivities = Activity::where('description', 'User logged in')->get();
```

## 5. Integration with v0.x-alpha Release

The `spatie/laravel-activitylog` package, integrated via the `Log` module, directly supports the
`System Monitor and User Activity Log` feature outlined in the `v0.x-alpha` release plan. It
provides the necessary infrastructure for tracking, storing, and retrieving significant events and
user interactions within the application.

---

**Navigation**

[← Previous: Spatie Laravel Permission Integration](spatie-laravel-permission.md) |
[Next: Spatie Laravel MediaLibrary Integration →](spatie-laravel-medialibrary.md)
