<?php

namespace Modules\Setup\Concerns\Livewire;

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
        $this->setupProps = [
            'currentStep' => $currentStep,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'extra' => $extra,
        ];
    }

    protected function ensurePrevStepCompleted(): void
    {
        if ($this->setupProps['prevStep'] && ! $this->setupService->isStepCompleted($this->setupProps['prevStep'])) {
            $prevRoute = 'setup.'.$this->setupProps['prevStep'];
            $this->redirectRoute($prevRoute, navigate: true);
        }
    }

    public function nextStep(): void
    {
        if ($this->setupProps['currentStep'] === 'complete') {
            $landingRoute = $this->setupProps['extra']['landing_route'] ?? 'login';

            $this->setupService->finalizeAppSetup();
            $this->redirectRoute($landingRoute, navigate: true);

            return;
        }

        $nextRoute = 'setup.'.$this->setupProps['nextStep'];

        $this->proceedNextStep();
        $this->redirectRoute($nextRoute, navigate: true);
    }

    protected function proceedNextStep(): bool
    {
        return $this->setupService->proceedNextStep($this->setupProps['currentStep'], $this->setupProps['nextStep'], $this->setupProps['extra']['req_record'] ?? '');
    }
}
