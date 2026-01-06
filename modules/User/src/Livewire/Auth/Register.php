<?php

namespace Modules\User\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService;

class Register extends Component
{
    protected AuthService $authService;

    #[Rule('required|string|min:3')]
    public string $name = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function boot(AuthService $authService): void
    {
        $this->authService = $authService;
    }

    public function register(): void
    {
        $validated = $this->validate();

        try {
            $user = $this->authService->register($validated);

            $this->authService->login([
                'email' => $user->email,
                'password' => $validated['password'],
            ]);

            $this->redirect(route('dashboard'), navigate: true);

        } catch (AppException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('user::livewire.auth.register')
            ->layout('user::components.layouts.auth', [
                'title' => __('Daftar Akun | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
