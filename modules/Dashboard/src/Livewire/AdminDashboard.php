<?php

declare(strict_types=1);

namespace Modules\Dashboard\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render(): View
    {
        return view('dashboard::livewire.admin-dashboard')->layout(
            'ui::components.layouts.base.with-navbar',
            [
                'title' => 'Dasbor Admin',
            ],
        );
    }
}
