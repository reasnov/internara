<?php

declare(strict_types=1);

namespace Modules\Permission\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Permission\Database\Factories\RoleFactory;
use Modules\Shared\Models\Concerns\HasUuid;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
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
     * Determine if the model should use UUIDs.
     */
    protected function usesUuid(): bool
    {
        return config('permission.model_key_type') === 'uuid';
    }

    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }
}
