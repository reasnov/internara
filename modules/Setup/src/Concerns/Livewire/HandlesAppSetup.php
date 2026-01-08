<?php

namespace Modules\Setup\Concerns\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Modules\Setup\Contracts\Services\SetupService;

/**
 * Handles the core logic for multi-step application setup wizards.
 * This trait manages step progression, completion state, and redirection,
 * intended for use within Livewire components that represent a setup step.
 *
 * @mixin Component
 */
trait HandlesAppSetup
{
    /**
     * The service responsible for handling setup business logic.
     */
    protected SetupService $setupService;

    /**
     * Holds the properties of the current setup step.
     */
    #[Locked]
    public array $setupProps = [];

    #[Computed()]
    public function disableNextStep(): bool
    {
        $record = $this->setupProps['extra']['req_record'] ?? null;

        return $record ? !$this->setupService->isRecordExists($record) : false;
    }

    /**
     * Initializes the properties for the current setup step.
     *
     * @param  string  $currentStep  The identifier for the current step.
     * @param  string  $nextStep  The identifier for the next step.
     * @param  string  $prevStep  The identifier for the previous step.
     * @param  array<string, mixed>  $extra  Additional data for the step.
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
     * Ensures the previous step was completed, redirecting if it was not.
     */
    protected function ensurePrevStepCompleted(): void
    {
        $prevStep = $this->setupProps['prevStep'] ?? null;

        if ($prevStep && ! $this->setupService->isStepCompleted($prevStep)) {
            $this->redirectToStep($prevStep);
        }
    }

    /**
     * Marks the current step as complete and proceeds to the next step.
     */
    public function nextStep(): void
    {
        $this->proceedNextStep();

        if (! empty($this->setupProps['nextStep'])) {
            $this->redirectToStep($this->setupProps['nextStep']);
        }
    }

    public function back(): void
    {
        $this->redirectToStep($this->setupProps['prevStep'] ?? 'welcome');
    }

    /**
     * Executes the logic for the current step and handles finalization if applicable.
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
     * Finalizes the application setup and redirects to the landing page.
     */
    protected function finalizeAppSetup(): void
    {
        $this->setupService->finalizeAppSetup();
        $this->redirectToLanding();
    }

    /**
     * Redirects to a named setup step route.
     *
     * @param  string  $name  The name of the step to redirect to.
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
