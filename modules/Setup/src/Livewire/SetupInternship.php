<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

/**
 * Represents the 'Internship Data' step in the application setup process.
 * This component is responsible for setting up initial internship-related data.
 */
class SetupInternship extends Component
{
    use HandlesAppSetup;

    /**
     * Boots the component and injects the SetupService.
     *
     * @param  \Modules\Setup\Contracts\Services\SetupService  $setupService  The service for handling setup logic.
     */
    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

    /**
     * Mounts the component, initializes setup properties, and ensures step progression is valid.
     */
    public function mount(): void
    {
        $this->initSetupProps(
            currentStep: 'internship',
            nextStep: 'complete',
            prevStep: 'department',
            extra: ['req_record' => 'internship']
        );

        $this->ensurePrevStepCompleted();
    }

    /**
     * Renders the component's view.
     *
     * @return \Illuminate\View\View The view for the internship setup step.
     */
    public function render(): View
    {
        return view('setup::livewire.setup-internship')
            ->layout('setup::components.layouts.setup', [
                'title' => __('Atur Data PKL | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
