# Spatie/Laravel-MediaLibrary Integration

This document outlines the integration of the `spatie/laravel-medialibrary` package within the Internara project, detailing its setup, custom configurations, and usage conventions. This package is essential for managing file uploads and associated media with various Eloquent models.

---

## 1. Overview

The `spatie/laravel-medialibrary` package provides a robust and flexible way to attach all sorts of files to Eloquent models. It handles storage, retrieves media, and can perform image manipulations.

## 2. Installation and Setup

The package was installed via Composer:

```bash
composer require "spatie/laravel-medialibrary:^11.0"
```

After installation, the following vendor assets were published:

*   **Migrations:**
    ```bash
    php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
    ```
    This copied the `create_media_table.php` migration to `database/migrations/`.
*   **Configuration:**
    ```bash
    php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-config"
    ```
    This copied `media-library.php` to `config/media-library.php`.

## 3. Customization and Integration (Media Module)

The `spatie/laravel-medialibrary` package is integrated within a dedicated `Media` module to maintain the project's modular architecture.

### 3.1. Media Model (`modules/Media/src/Models/Media.php`)

The default `Media` model provided by the package has been extended and customized within `modules/Media/src/Models/Media.php`:

*   **`$fillable` Properties:** The `$fillable` array has been explicitly defined to include all mass assignable fields from the `media` table migration, such as `model_type`, `model_id`, `uuid`, `collection_name`, `name`, `module`, `file_name`, `mime_type`, `disk`, `conversions_disk`, `size`, `manipulations`, `custom_properties`, `generated_conversions`, `responsive_images`, `order_column`, `created_at`, and `updated_at`.
*   **`module` Column:** A custom `module` field has been added to the `$fillable` array and the database migration to track which module a specific media item belongs to.
*   **`model_id` Casting:** The `model_id` attribute is explicitly cast to `string` (`protected $casts = ['model_id' => 'string'];`). This is crucial for supporting polymorphic relationships with models that use either **UUIDs** (stored as strings) or **big integers** (which are stringified) as their primary keys.

### 3.2. Media Table Migration (`modules/Media/database/migrations/2026_01_06_234237_create_media_table.php`)

The `create_media_table.php` migration has been modified to ensure compatibility with models using diverse primary key types:

*   **Explicit `model_type` and `model_id`:** The default `$table->morphs('model');` has been replaced with explicit string column definitions:
    ```php
                $table->string('model_type');
                $table->string('model_id', 36); // Accommodates UUIDs
                $table->index(['model_type', 'model_id']);
    ```
    This change allows the `model_id` column to correctly store both UUID strings and string representations of `bigint` IDs, maintaining flexibility for polymorphic relations.

## 4. Usage Conventions

To utilize the `MediaLibrary` effectively:

*   **Implement `HasMedia`:** Any Eloquent model that needs to associate with media must implement the `Spatie\MediaLibrary\HasMedia` interface and use the `Spatie\MediaLibrary\InteractsWithMedia` trait.
*   **Register Media Collections:** Within your model, define media collections using the `registerMediaCollections()` method.
*   **Attaching Media:** Use the `addMedia()` method provided by the trait to attach files.
*   **Retrieving Media:** Media can be retrieved via the `getMedia()` method.

**Example of an Eloquent Model using MediaLibrary:**

```php
<?php

namespace Modules\User\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media; // Import the custom Media model if needed

class User extends Authenticatable implements HasMedia
{
    use InteractsWithMedia;

    // ... other model properties ...

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')->singleFile();
        $this->addMediaCollection('documents');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->width(100)
              ->height(100);
    }
}
```

*   **Using the Custom `Media` Model:** If you need to leverage the custom `module` field or any other customizations in the `Media` model, ensure you are interacting with `Modules\Media\Models\Media` rather than the base `Spatie\MediaLibrary\MediaCollections\Models\Media`. You may need to specify this in the `media-library.php` config or by overriding `getMediaModel()` in your `HasMedia` models.
