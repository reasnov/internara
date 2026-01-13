<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Modules\School\Database\Factories\SchoolFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class School extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'fax',
        'principal_name',
    ];

    protected static function newFactory(): SchoolFactory
    {
        return SchoolFactory::new();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('school_logo')
            ->singleFile();
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('school_logo') ?: null;
    }

    public function changeLogo(string|UploadedFile $file, string $collectionName = 'school_logo'): bool
    {
        $this->clearMediaCollection('school_logo');

        return (bool) $this->addMedia($file)->toMediaCollection($collectionName) ?? false;
    }
}
