<?php

namespace Modules\Internship\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Internship\Database\Factories\InternshipFactory;
use Modules\School\Models\Concerns\HasSchoolRelation;

class Internship extends Model
{
    use HasUuids;
    use HasFactory;
    use HasSchoolRelation;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'year',
        'semester',
        'date_start',
        'date_finish',
        'school_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'year' => 'year',
        'semester' => 'string',
        'date_start' => 'date',
        'date_finish' => 'date'
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return InternshipFactory
     */
    protected static function newFactory(): InternshipFactory
    {
        return InternshipFactory::new();
    }
}
