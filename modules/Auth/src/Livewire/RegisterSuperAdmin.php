<?php

declare(strict_types=1);

namespace Modules\Auth\Livewire;

use Livewire\Component;
use Modules\Auth\Services\Contracts\AuthService;
use Modules\User\Livewire\UserForm;

class RegisterSuperAdmin extends Component
{
    public UserForm $form;

    protected AuthService $authService;

    public function boot(
        \Modules\Auth\Services\Contracts\AuthService $authService,
        \Modules\User\Services\Contracts\SuperAdminService $superAdminService,
    ): void {
        $this->authService = $authService;
        $this->form->id = $superAdminService->get(['id'])?->id;
    }

    public function mount()
    {
        $this->form->name = 'Administrator';
    }

    public function register()
    {
        $this->form->validate();

        $registeredUser = $this->authService->register($this->form->except('id'), 'super-admin');

        if ($registeredUser) {
            $this->dispatch('super-admin-registered', userId: $registeredUser->getKey());
        }
    }

    public function render()
    {
        return view('auth::livewire.register-super-admin')->layout(
            'auth::components.layouts.auth',
            [
                'title' => __('Buat Akun Utama | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ],
        );
    }
}
