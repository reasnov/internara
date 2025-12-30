<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

class SetupDepartment extends Component
{
    use HandlesAppSetup;

    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

    public function mount(): void
    {
        $this->initSetupProps(
            currentStep: 'department',
            nextStep: 'program',
            prevStep: 'school',
            extra: ['req_record' => 'department']
        );
    }

    public function render(): View
    {
        return view('setup::livewire.setup-department')
            ->layout('setup::components.layouts.setup', [
                'title' => 'Atur Data Jurusan | Internara - Sistem Informasi Manajemen PKL',
            ]);
    }
}
