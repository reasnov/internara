<?php

namespace Modules\User\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService;

/**
 * Livewire component for handling user login.
 *
 * This component provides the interface and logic for users to log in to the application.
 * It delegates authentication logic to the `AuthService`.
 */
class Login extends Component
{
    protected AuthService $authService;

    /**
     * The user's email address or username for login.
     */
    #[Rule('required|string|email')]
    public string $email = '';

    #[Rule('required|string')]
    public string $password = '';

    /**
     * Indicates whether the user should be remembered.
     */
    public bool $remember = false;

    /**
     * Initializes the component with the AuthService.
     *
     * @param  \Modules\User\Contracts\Services\AuthService  $authService  The authentication service.
     */
    public function boot(AuthService $authService): void
    {
        $this->authService = $authService;
    }

    /**
     * Handles the login attempt.
     *
     * Validates the credentials, attempts to log in the user via AuthService,
     * and redirects to the dashboard on success or adds an error on failure.
     */
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
            $this->addError('email', $e->getUserMessage());
        }
    }

    /**
     * Renders the login view.
     */
    public function render(): View
    {
        return view('user::livewire.auth.login')
            ->layout('user::components.layouts.auth', [
                'title' => __('Login to Dashboard | :site_title', [
                    'site_title' => setting('site_title', 'Internara'),
                ]),
            ]);
    }
}
