<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

/**
 * Represents the 'Department Setup' step in the application setup process.
 * This component is responsible for setting up initial department data.
 */
class SetupDepartment extends Component
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
            currentStep: 'department',
            nextStep: 'internship',
            prevStep: 'school',
            extra: ['req_record' => 'department']
        );

        $this->ensurePrevStepCompleted();
    }

    /**
     * Renders the component's view.
     *
     * @return \Illuminate\View\View The view for the department setup step.
     */
    public function render(): View
    {
        return view('setup::livewire.setup-department')
            ->layout('setup::components.layouts.setup', [
                'title' => __('Atur Data Jurusan | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
