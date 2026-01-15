<?php

declare(strict_types=1);

namespace Modules\Internship\Livewire;

use Livewire\Component;
use Modules\Shared\Livewire\Concerns\ManagesRecords;

class InternshipManager extends Component
{
    use ManagesRecords;

    public function mount(
        \Modules\Internship\Services\Contracts\InternshipService $internshipService,
    ): void {
        $this->service = $internshipService;
        $this->eventPrefix = 'internship';
    }

    public function render()
    {
        return view('internship::livewire.internship-manager');
    }
}
