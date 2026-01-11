<?php

namespace Modules\Department\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Support\Concerns\Livewire\HandlesRecords;

class DepartmentManager extends Component
{
    use HandlesRecords;

    public function mount(): void
    {
        $this->prepRecordHandler(['recordName' => 'department']);
    }

    public function render(): View
    {
        return view('department::livewire.department-manager');
    }
}
