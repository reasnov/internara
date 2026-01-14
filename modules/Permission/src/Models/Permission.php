<?php

namespace Modules\Permission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Permission\Database\Factories\PermissionFactory;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use HasFactory;

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
     * Create a new Eloquent model instance.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (config('permission.model_key_type') === 'uuid') {
            $this->incrementing = false;
            $this->keyType = 'string';
        }
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Permission $permission) {
            if (config('permission.model_key_type') === 'uuid' && empty($permission->id)) {
                $permission->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected static function newFactory(): PermissionFactory
    {
        return PermissionFactory::new();
    }
}
