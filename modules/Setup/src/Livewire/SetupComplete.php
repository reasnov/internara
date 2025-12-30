<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

class SetupComplete extends Component
{
    use HandlesAppSetup;

    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

    public function mount(): void
    {
        $this->initSetupProps(
            currentStep: 'complete',
            prevStep: 'program',
        );
    }

    public function render(): View
    {
        return view('setup::livewire.setup-complete')
            ->layout('setup::components.layouts.setup', [
                'title' => 'Satu Langkah Lagi | Internara - Sistem Informasi Manajemen PKL',
            ]);
    }
}
