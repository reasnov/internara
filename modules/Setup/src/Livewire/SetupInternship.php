<?php

namespace Modules\Setup\Livewire;

use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

class SetupInternship extends Component
{
    use HandlesAppSetup;

    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

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

    public function render()
    {
        return view('setup::livewire.setup-internship')
            ->layout('setup::components.layouts.setup', [
                'title' => __('Atur Data PKL | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
