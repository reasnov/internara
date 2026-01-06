<?php

namespace Modules\Setup\Contracts\Services;

interface SetupService
{
    public function isStepCompleted(string $step): bool;

    public function proceedSetupStep(string $step, ?string $requireRecord = null): bool;

    public function finalizeAppSetup(): bool;

    public function isAppInstalled(): bool;
}
