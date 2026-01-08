<?php

namespace Modules\Internship\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Internship\Database\Factories\InternshipFactory;

class Internship extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    /**
     * Create a new factory instance for the model.
     *
     * @return InternshipFactory
     */
    // protected static function newFactory(): InternshipFactory
    // {
    //     // return InternshipFactory::new();
    // }
}
