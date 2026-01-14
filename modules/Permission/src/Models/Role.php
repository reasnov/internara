<?php

namespace Modules\Permission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Permission\Database\Factories\RoleFactory;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
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
     * Set the role's name to StudlyCase.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = Str::studly($value);
    }

    /**
     * Get the role's name in StudlyCase.
     */
    public function getNameAttribute(string $value): string
    {
        return Str::studly($value);
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
        static::creating(function (Role $role) {
            if (config('permission.model_key_type') === 'uuid' && empty($role->id)) {
                $role->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }
}
