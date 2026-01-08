<?php

namespace Modules\User\Livewire\Auth;

use Livewire\Component;
use Modules\User\Contracts\Services\AuthService;
use Modules\User\Contracts\Services\OwnerService;
use Modules\User\Livewire\AccountForm;

class RegisterOwner extends Component
{
    public AccountForm $form;

    protected AuthService $authService;

    public function boot(AuthService $authService, OwnerService $ownerService): void
    {
        $this->authService = $authService;
        $this->form->id = $ownerService->get(['id'])?->id;
    }

    public function mount()
    {
        $this->form->name = 'Administrator';
    }

    public function register()
    {
        $this->form->validate();

        $registeredUser = $this->authService->register($this->form->except('id'), 'owner');

        if ($registeredUser) {
            $this->dispatch('owner-registered', userId: $registeredUser->getKey());
        }
    }

    public function render()
    {
        return view('user::livewire.auth.register-owner')
            ->layout('user::components.layouts.auth', [
                'title' => __('Buat Akun Utama | :site_title', [
                    'site_title' => setting('site_title', 'Internara')
                ])
            ]);
    }
}
