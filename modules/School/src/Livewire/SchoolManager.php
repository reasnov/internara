<?php

declare(strict_types=1);

namespace Modules\School\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\School\Livewire\Forms\SchoolForm;
use Modules\School\Services\Contracts\SchoolService;

class SchoolManager extends Component
{
    use WithFileUploads;

    public SchoolForm $form;

    protected SchoolService $schoolService;

    public function boot(SchoolService $schoolService): void
    {
        $this->schoolService = $schoolService;
    }

    public function mount(): void
    {
        $this->initFormData();
    }

    protected function initFormData(): void
    {
        $school = $this->schoolService->first()?->append('logo_url');

        if ($school) {
            $this->form->fill($school);
        }
    }

    public function save(): void
    {
        $this->form->validate();

        $school = $this->schoolService->save($this->form->all());

        // Refresh form with persisted data, including new logo URLs
        $this->form->fill($school);

        $this->dispatch('school_saved', schoolId: $school->id);
        $this->dispatch('notify', message: 'Data sekolah berhasil disimpan.', type: 'success');
    }

    public function render()
    {
        return view('school::livewire.school-manager');
    }
}
