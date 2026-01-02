<?php

namespace Modules\User\Livewire\Auth;

use Livewire\Component;

class Register extends Component
{
    public function render()
    {
        return view('user::livewire.auth.register')
            ->layout('user::components.layouts.auth', [
                'title' => 'Daftar Akun | ' . setting('site_title', 'Internara - Sistem Informasi Manajemen PKL')
            ]);
    }
}
