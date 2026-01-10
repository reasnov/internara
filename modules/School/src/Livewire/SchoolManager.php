<?php

namespace Modules\School\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Modules\School\Contracts\Services\SchoolService;
use Modules\School\Livewire\Forms\SchoolForm;

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
        // Populate the form with the first school's data, as this is a manager component.
        $school = $this->schoolService->first();

        if ($school) {
            $this->form->fill($school);
        }
    }

    public function save(): void
    {
        $this->form->validate();

        $school = $this->schoolService->updateOrCreate($this->form->all());

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
