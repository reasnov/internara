<?php

namespace Modules\Setup\Contracts\Services;

/**
 * Defines the contract for the service that handles application setup logic.
 */
interface SetupService
{
    /**
     * Checks if a specific setup step has been completed.
     *
     * @param  string  $step  The identifier of the step to check.
     * @return bool True if the step is completed, false otherwise.
     */
    public function isStepCompleted(string $step): bool;

    /**
     * Proceeds with a setup step, validating requirements and marking it as complete.
     *
     * @param  string  $step  The identifier of the step to proceed with.
     * @param  string|null  $requireRecord  The type of record required for this step to be valid.
     * @return bool True on success.
     *
     * @throws \Modules\Shared\Exceptions\RecordNotFoundException If a required record does not exist.
     * @throws \InvalidArgumentException If an unknown record type is required.
     */
    public function proceedSetupStep(string $step, ?string $requireRecord = null): bool;

    /**
     * Finalizes the application setup by persisting final settings.
     *
     * @return bool True if the app is marked as installed.
     */
    public function finalizeAppSetup(): bool;

    public function isRecordExists(string $record): bool;

    /**
     * Checks if the application is marked as installed.
     *
     * @return bool True if installed, false otherwise.
     */
    public function isAppInstalled(): bool;

    /**
     * Ensures the setup process is allowed to run.
     *
     * @throws \Modules\Shared\Exceptions\AppException If the application is already installed.
     */
    public function requireSetupAccess(): void;
}
