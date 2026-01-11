<?php

namespace Modules\School\Concerns\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;
use Modules\School\Contracts\Services\SchoolService;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasSchoolRelation
{
    protected function changeSchoolId(mixed $schoolId = null): bool
    {
        $schoolService = app()->make(SchoolService::class);

        // Always use first school id record if it is single record
        if (config('school.single_record', true) && !$schoolId) {
            $schoolId = $schoolService->first(['id'])?->id;
        }

        // Skip if $schoolId is empty
        if (!$schoolId || $this->school_id === $schoolId) {
            return true;
        }

        // Validate $schoolId
        $schoolService->query()->findOrFail($schoolId);

        $this->school_id = $schoolId;
        return $this->save();
    }

    public function school(): BelongsTo
    {
        $schoolService = app()->make(SchoolService::class);
        return $schoolService->registerFromRelatedModel($this, 'school_id');
    }
}
