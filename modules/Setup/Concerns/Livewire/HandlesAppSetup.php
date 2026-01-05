<?php

namespace Modules\Setup\Concerns\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Modules\Setup\Contracts\Services\SetupService;

/**
 * @mixin Component
 */
trait HandlesAppSetup
{
    protected SetupService $setupService;

    #[Locked()]
    public array $setupProps = [];

    protected function initSetupProps(string $currentStep, string $nextStep = '', string $prevStep = '', array $extra = [])
    {
        $this->requireSetupAccess();

        $this->setupProps = [
            'currentStep' => $currentStep,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'extra' => $extra,
        ];
    }

    protected function requireSetupAccess(): bool
    {
        $isAppInstalled = $this->setupService->isAppInstalled();
        if ($isAppInstalled) {
            $this->redirectToLanding();
        }

        return $isAppInstalled;
    }

    protected function ensurePrevStepCompleted(): void
    {
        $prevStep = $this->setupProps['prevStep'] ?? null;
        $sessionKey = "setup:{$prevStep}";

        if ($this->setupProps['prevStep'] && ! Session::get($sessionKey, false)) {
            $this->redirectToStep($this->setupProps['prevStep']);
        }
    }

    public function nextStep(): void
    {
        if ($this->setupProps['currentStep'] === 'complete') {
            $this->finalizeAppSetup();
            return;
        }

        $this->proceedNextStep();
        $this->redirectToStep($this->setupProps['nextStep']);
    }

    protected function proceedNextStep(): bool
    {
        $success = $this->setupService->proceedNextStep($this->setupProps['currentStep'], $this->setupProps['nextStep'], $this->setupProps['extra']['req_record'] ?? '');

        if ($success) {
            $currentStep = $this->setupProps['currentStep'];
            $sessionKey = "setup:{$currentStep}";
            Session::put($sessionKey, $success);
        }

        return $success;
    }

    protected function finalizeAppSetup(): void
    {

        $this->setupService->finalizeAppSetup();
        $this->redirectToLanding();
    }

    protected function redirectToStep(string $name): void
    {
        $routeName = "setup.{$name}";
        $this->redirectRoute($routeName, navigate: true);
    }

    protected function redirectToLanding(): void
    {
        Session::flush();

        $landingRoute = $this->setupProps['extra']['landing_route'] ?? 'login';
        $this->redirectRoute($landingRoute, navigate: true);
    }
}
