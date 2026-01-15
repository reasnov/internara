<?php

declare(strict_types=1);

namespace Modules\Department\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Department\Database\Factories\DepartmentFactory;
use Modules\School\Models\Concerns\HasSchoolRelation;

class Department extends Model
{
    use HasFactory;
    use HasSchoolRelation;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'description', 'school_id'];

    protected static function newFactory(): DepartmentFactory
    {
        return DepartmentFactory::new();
    }
}
