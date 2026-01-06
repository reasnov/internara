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

    #[Locked]
    public array $setupProps = [];

    /**
     * Initializes the properties for the current setup step.
     */
    protected function initSetupProps(string $currentStep, string $nextStep = '', string $prevStep = '', array $extra = []): void
    {
        $this->setupProps = [
            'currentStep' => $currentStep,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'extra' => $extra,
        ];
    }

    /**
     * Ensures that the previous step was completed before proceeding.
     * If not, it redirects the user back to the incomplete previous step.
     */
    protected function ensurePrevStepCompleted(): void
    {
        $prevStep = $this->setupProps['prevStep'] ?? null;

        if ($prevStep && ! $this->setupService->isStepCompleted($prevStep)) {
            $this->redirectToStep($prevStep);
        }
    }

    /**
     * Proceeds to the next step in the setup process.
     */
    public function nextStep(): void
    {
        $this->proceedNextStep();

        if (! empty($this->setupProps['nextStep'])) {
            $this->redirectToStep($this->setupProps['nextStep']);
        }
    }

    /**
     * Marks the current step as completed and handles finalization.
     */
    protected function proceedNextStep(): void
    {
        $this->setupService->proceedSetupStep(
            $this->setupProps['currentStep'],
            $this->setupProps['extra']['req_record'] ?? null
        );

        if ($this->setupProps['currentStep'] === 'complete') {
            $this->finalizeAppSetup();
        }
    }

    /**
     * Finalizes the application setup process.
     */
    protected function finalizeAppSetup(): void
    {
        $this->setupService->finalizeAppSetup();
        $this->redirectToLanding();
    }

    /**
     * Redirects to a named setup step route.
     */
    protected function redirectToStep(string $name): void
    {
        if (empty($name)) {
            return;
        }

        $routeName = "setup.{$name}";
        $this->redirectRoute($routeName, navigate: true);
    }

    /**
     * Flushes the session and redirects to the application's landing page.
     */
    protected function redirectToLanding(): void
    {
        Session::flush();

        $landingRoute = $this->setupProps['extra']['landing_route'] ?? 'login';
        $this->redirectRoute($landingRoute, navigate: true);
    }
}
