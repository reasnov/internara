<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

/**
 * Represents the 'Account Creation' step in the application setup process.
 * This component is responsible for handling the creation of the initial owner account.
 */
class SetupAccount extends Component
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
            currentStep: 'account',
            nextStep: 'school',
            prevStep: 'welcome',
            extra: ['req_record' => 'owner']
        );

        $this->ensurePrevStepCompleted();
    }

    /**
     * Handles the 'owner-registered' event to proceed to the next setup step.
     */
    #[On('owner-registered')]
    public function handleOwnerRegistered(): void
    {
        $this->nextStep();
    }

    /**
     * Renders the component's view.
     *
     * @return \Illuminate\View\View The view for the account setup step.
     */
    public function render(): View
    {
        return view('setup::livewire.setup-account')
            ->layout('setup::components.layouts.setup', [
                'title' => __('Buat Akun Administrator | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
