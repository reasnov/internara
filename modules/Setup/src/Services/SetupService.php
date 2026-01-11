<?php

namespace Modules\Setup\Services;

use Illuminate\Support\Facades\Session;
use InvalidArgumentException;
use Modules\Department\Contracts\Services\DepartmentService;
use Modules\Internship\Contracts\Services\InternshipService;
use Modules\School\Contracts\Services\SchoolService;
use Modules\Setting\Contracts\Services\SettingService;
use Modules\Setup\Contracts\Services\SetupService as SetupServiceContract;
use Modules\Shared\Exceptions\AppException;
use Modules\Shared\Exceptions\RecordNotFoundException;
use Modules\User\Contracts\Services\OwnerService;

/**
 * Service implementation for handling the application setup process.
 */
class SetupService implements SetupServiceContract
{
    public function __construct(
        protected SettingService $settingService,
        protected OwnerService $ownerService,
        protected SchoolService $schoolService,
        protected DepartmentService $departmentService,
        protected InternshipService $internshipService,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function isStepCompleted(string $step): bool
    {
        if (empty($step)) {
            return true;
        }

        return Session::get("setup:{$step}", false);
    }

    /**
     * {@inheritDoc}
     */
    public function proceedSetupStep(string $step, ?string $requireRecord = null): bool
    {
        if (isset($requireRecord)) {
            $this->ensureRecordExists($requireRecord);
        }

        $this->storeStep($step, true);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function finalizeAppSetup(): bool
    {
        $schoolRecord = $this->schoolService->get();
        $settings = [
            'brand_name' => $schoolRecord->name,
            'brand_logo' => $schoolRecord->logo_url ?? null,
            'brand_logo_dark' => $schoolRecord->logo_dark_url ?? null,
            'site_title' => $schoolRecord->name,
            'app_installed' => true,
        ];

        $this->settingService->set($settings);

        return $this->isAppInstalled();
    }

    /**
     * {@inheritDoc}
     */
    public function isAppInstalled(): bool
    {
        return $this->settingService->get('app_installed', false);
    }

    /**
     * {@inheritDoc}
     */
    public function requireSetupAccess(): void
    {
        if ($this->isAppInstalled()) {
            throw new AppException(userMessage: 'Setup already completed.', code: 403);
        }
    }

    public function isRecordExists(string $record): bool
    {
        return match ($record) {
            'owner' => $this->ownerService->exists(),
            'school' => $this->schoolService->exists(),
            'department' => $this->departmentService->exists(),
            'internship' => $this->internshipService->exists(),
            default => throw new InvalidArgumentException("Unknown record type '{$record}' requested."),
        };
    }

    /**
     * Ensures that a specific type of record exists in the database.
     *
     * @param  string  $record  The type of record to check (e.g., 'owner', 'school').
     * @return bool True if the record exists.
     *
     * @throws \Modules\Shared\Exceptions\RecordNotFoundException
     * @throws \InvalidArgumentException
     */
    protected function ensureRecordExists(string $record = ''): bool
    {
        $isExists = $this->isRecordExists($record);

        if (! $isExists) {
            throw new RecordNotFoundException('records::exceptions.not_found');
        }

        return $isExists;
    }

    /**
     * Stores the completion status of a setup step in the session.
     *
     * @param  string  $name  The name of the step.
     * @param  bool  $completed  The completion status.
     */
    protected function storeStep(string $name, bool $completed = true): void
    {
        Session::put("setup:{$name}", $completed);
    }
}
