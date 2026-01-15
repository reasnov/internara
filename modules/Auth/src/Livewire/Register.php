<?php

declare(strict_types=1);

namespace Modules\Auth\Livewire;

use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Modules\Auth\Services\Contracts\AuthService;
use Modules\Auth\Services\Contracts\RedirectService;
use Modules\Exception\AppException;

class Register extends Component
{
    protected AuthService $authService;

    protected RedirectService $redirectService;

    #[Rule('required|string|min:3')]
    public string $name = '';

    #[Rule('required|email|unique:users,email')]
    public string $email = '';

    #[Rule('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    public function boot(AuthService $authService, RedirectService $redirectService): void
    {
        $this->authService = $authService;
        $this->redirectService = $redirectService;
    }

    public function register(): void
    {
        $validated = $this->validate();

        try {
            // Register user and trigger email verification
            $user = $this->authService->register($validated, sendEmailVerification: true);

            $this->authService->login([
                'email' => $user->email,
                'password' => $validated['password'],
            ]);

            $this->redirect($this->redirectService->getTargetUrl($user), navigate: true);
        } catch (AppException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('auth::livewire.register')->layout('auth::components.layouts.auth', [
            'title' => __('Daftar Akun | :site_title', [
                'site_title' => setting('site_title', 'Internara'),
            ]),
        ]);
    }
}
