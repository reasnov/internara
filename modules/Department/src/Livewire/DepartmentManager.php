<?php

namespace Modules\Department\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Shared\Livewire\Concerns\ManagesRecords;

class DepartmentManager extends Component
{
    use ManagesRecords;

    public function mount(\Modules\Department\Services\Contracts\DepartmentService $departmentService): void
    {
        $this->service = $departmentService;
    }

    public function render(): View
    {
        return view('department::livewire.department-manager');
    }
}
