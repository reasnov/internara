<?php

namespace Modules\Setup\Contracts\Services;

interface SetupService
{
    public function isStepCompleted(string $step): bool;

    public function proceedNextStep(string $currentStep, string $nextStep, string $requireRecord = ''): bool;

    public function finalizeAppSetup(): bool;
}
