<?php

namespace Modules\Department\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\Shared\Concerns\Livewire\ManagesRecords;

class DepartmentManager extends Component
{
    use ManagesRecords;

    public function mount(): void
    {
        //
    }

    public function render(): View
    {
        return view('department::livewire.department-manager');
    }
}
