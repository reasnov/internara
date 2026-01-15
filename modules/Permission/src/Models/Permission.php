<?php

declare(strict_types=1);

namespace Modules\Permission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Permission\Database\Factories\PermissionFactory;
use Modules\Shared\Models\Concerns\HasUuid;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use HasFactory;
    use HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'module',
    ];

    /**
     * Set the permission's name to lowercase dot.notation.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = Str::lower(str_replace(' ', '.', $value));
    }

    /**
     * Get the permission's name in lowercase dot.notation.
     */
    public function getNameAttribute(string $value): string
    {
        return Str::lower(str_replace(' ', '.', $value));
    }

    /**
     * Determine if the model should use UUIDs.
     */
    protected function usesUuid(): bool
    {
        return config('permission.model_key_type') === 'uuid';
    }

    protected static function newFactory(): PermissionFactory
    {
        return PermissionFactory::new();
    }
}
