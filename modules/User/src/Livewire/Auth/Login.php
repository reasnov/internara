<?php

namespace Modules\User\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService;

class Login extends Component
{
    protected AuthService $authService;

    #[Rule('required|string')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function boot(AuthService $authService): void
    {
        $this->authService = $authService;
    }

    public function login(): void
    {
        $this->validate();

        try {
            $this->authService->login([
                'email' => $this->email,
                'password' => $this->password,
            ], $this->remember);

            $this->redirect(route('dashboard'), navigate: true);
        } catch (AppException $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('user::livewire.auth.login')
            ->layout('user::components.layouts.auth', [
                'title' => __('Masuk ke Dasbor | :site_title', [
                    'site_title' => 'Internara'
                ]),
            ]);
    }
}
