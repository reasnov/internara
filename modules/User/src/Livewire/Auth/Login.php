<?php

namespace Modules\User\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Component;

class Login extends Component
{
    public function render(): View
    {
        return view('user::livewire.auth.login')
            ->layout('user::components.layouts.auth', [
                'title' => 'Masuk ke Dasbor | ' . setting('site_name', 'Internara')
            ]);
    }
}
