<?php

namespace Modules\Internship\Livewire;

use Livewire\Component;
use Modules\Support\Concerns\Livewire\HandlesRecords;

class InternshipManager extends Component
{
    use HandlesRecords;

    public function mount(): void
    {
        $this->prepRecordHandler(['recordName' => 'internship']);
    }

    public function render()
    {
        return view('internship::livewire.internship-manager');
    }
}
