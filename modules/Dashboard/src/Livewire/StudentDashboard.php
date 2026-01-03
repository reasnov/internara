<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Illuminate\View\View;

class StudentDashboard extends Component
{
    public function render(): View
    {
        return view('dashboard::livewire.student-dashboard')
            ->layout('ui::components.layouts.base.with-navbar', [
                'title' => 'Dasbor Siswa',
            ]);
    }
}