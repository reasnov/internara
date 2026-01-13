<?php

namespace Modules\Auth\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Modules\Exception\AppException;
use Modules\Auth\Services\Contracts\AuthService;

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

    public function boot(\Modules\Auth\Services\Contracts\AuthService $authService): void
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
        return view('auth::livewire.register')
            ->layout('auth::components.layouts.auth', [
                'title' => __('Daftar Akun | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
