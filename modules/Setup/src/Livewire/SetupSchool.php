<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

/**
 * Represents the 'School Setup' step in the application setup process.
 * This component is responsible for setting up initial school data.
 */
class SetupSchool extends Component
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
            currentStep: 'school',
            nextStep: 'department',
            prevStep: 'account',
            extra: ['req_record' => 'school']
        );

        $this->ensurePrevStepCompleted();
    }

    /**
     * Listens for the 'school-updated' event and proceeds to the next step.
     */
    #[On('school-updated')]
    public function handleSchoolUpdated(): void
    {
        $this->nextStep();
    }

    /**
     * Renders the component's view.
     *
     * @return \Illuminate\View\View The view for the school setup step.
     */
    public function render(): View
    {
        return view('setup::livewire.setup-school')
            ->layout('setup::components.layouts.setup', [
                'title' => __('Atur Data Sekolah | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
