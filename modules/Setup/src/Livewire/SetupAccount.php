<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

class SetupAccount extends Component
{
    use HandlesAppSetup;

    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

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
