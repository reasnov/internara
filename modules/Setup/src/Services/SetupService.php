<?php

namespace Modules\Setup\Services;

use Illuminate\Support\Facades\Session;
use Modules\Department\Contracts\Services\DepartmentService;
use Modules\Internship\Contracts\Services\InternshipService;
use Modules\School\Contracts\Services\SchoolService;
use Modules\Setting\Contracts\Services\SettingService;
use Modules\Setup\Contracts\Services\SetupService as SetupServiceContract;
use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\User\Contracts\Services\OwnerService;

class SetupService implements SetupServiceContract
{
    public function __construct(
        protected SettingService $settingService,
        protected OwnerService $ownerService,
        protected SchoolService $schoolService,
        protected DepartmentService $departmentService,
        protected InternshipService $internshipService,
    ) {}

    public function isStepCompleted(string $step): bool
    {
        return Session::get("setup:{$step}", false);
    }

    public function proceedSetupStep(string $step, ?string $requireRecord = null): bool
    {
        // Validate requirements
        if (isset($requireRecord)) {
            $this->ensureRecordExists($requireRecord);
        }

        // Logic to mark the step as completed, possibly involving $requireRecord
        $this->storeStep($step, true);

        return true;
    }

    public function finalizeAppSetup(): bool
    {
        $schoolRecord = $this->schoolService->get();
        $settings = [
            'brand_name' => $schoolRecord->name,
            'brand_logo' => $schoolRecord->logo ?? null,
            'brand_logo_dark' => $schoolRecord->logo_dark ?? null,
            'site_title' => $schoolRecord->name,
            'app_installed' => true,
        ];

        $this->settingService->set($settings);

        return $this->isAppInstalled();
    }

    public function isAppInstalled(): bool
    {
        return $this->settingService->get('app_installed', false);
    }

    protected function ensureRecordExists(string $record = ''): bool
    {
        $isExists = match ($record) {
            'owner' => $this->ownerService->exists(),
            'school' => $this->schoolService->exists(),
            'department' => $this->departmentService->exists(),
            'internship' => $this->internshipService->exists(),
            default => false,
        };

        if (! $isExists) {
            throw new RecordNotFoundException('shared::exceptions.record_not_found');
        }

        return $isExists;
    }

    protected function storeStep(string $name, bool $completed = true): void
    {
        Session::put("setup:{$name}", $completed);
    }
}
