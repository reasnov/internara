<?php

namespace Modules\Setup\Contracts\Services;

interface SetupService
{
    public function proceedNextStep(string $currentStep, string $nextStep, string $requireRecord = ''): bool;

    public function finalizeAppSetup(): bool;

    public function isAppInstalled(): bool;
}
