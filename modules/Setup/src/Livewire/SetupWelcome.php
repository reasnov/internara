<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

class SetupWelcome extends Component
{
    use HandlesAppSetup;

    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

    public function mount(): void
    {
        $this->initSetupProps(
            currentStep: 'welcome',
            nextStep: 'account',
        );
    }

    public function render(): View
    {
        return view('setup::livewire.setup-welcome')
            ->layout('setup::components.layouts.setup', [
                'title' => __('Mulai Instalasi | :site_title', [
                    'site_title' => setting('site_title', 'Internara')
                ])
            ]);
    }
}
