<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Illuminate\View\View;

class TeacherDashboard extends Component
{
    public function render(): View
    {
        return view('dashboard::livewire.teacher-dashboard')
            ->layout('ui::components.layouts.base.with-navbar', [
                'title' => 'Dasbor Guru',
            ]);
    }
}