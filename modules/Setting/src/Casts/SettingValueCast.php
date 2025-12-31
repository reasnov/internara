<?php

namespace Modules\Setting\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class SettingValueCast implements CastsAttributes
{
    /**
     * Cast the given value from the database.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        // If the 'type' attribute is not set, default to string
        $type = $attributes['type'] ?? 'string';

        return match ($type) {
            'json' => json_decode($value, true),
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            default => $value, // string or other types
        };
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        $type = gettype($value);

        if ($type === 'array' || $type === 'object') {
            $value = json_encode($value);
            $type = 'json';
        } elseif ($type === 'boolean') {
            $value = (int) $value; // Store boolean as 0 or 1
            $type = 'boolean';
        }

        // Store the original type and the stringified value
        return [
            'value' => (string) $value,
            'type' => $type,
        ];
    }
}
