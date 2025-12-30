<?php

namespace Modules\Setup\Livewire;

use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

class SetupProgram extends Component
{
    use HandlesAppSetup;

    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

    public function mount(): void
    {
        $this->initSetupProps(
            currentStep: 'program',
            nextStep: 'complete',
            prevStep: 'department',
            extra: ['req_record' => 'program']
        );
    }

    public function render()
    {
        return view('setup::livewire.setup-program')
            ->layout('setup::components.layouts.setup', [
                'title' => 'Atur Data PKL | Internara - Sistem Informasi Manajemen PKL',
            ]);
    }
}
