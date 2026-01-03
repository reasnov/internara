<?php

namespace Modules\Dashboard\Livewire;

use Livewire\Component;
use Illuminate\View\View;

class AdminDashboard extends Component
{
    public function render(): View
    {
        return view('dashboard::livewire.admin-dashboard')
            ->layout('ui::components.layouts.base.with-navbar', [
                'title' => 'Dasbor Admin',
            ]);
    }
}