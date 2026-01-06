<?php

namespace Modules\Setup\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Modules\Setup\Concerns\Livewire\HandlesAppSetup;
use Modules\Setup\Contracts\Services\SetupService;

class SetupSchool extends Component
{
    use HandlesAppSetup;

    public function boot(SetupService $setupService): void
    {
        $this->setupService = $setupService;
    }

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

    #[On('school-updated')]
    public function handleSchoolUpdated(): void
    {
        $this->nextStep();
    }

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
