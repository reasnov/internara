<?php

namespace Modules\User\Livewire\Auth;

use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Modules\Exceptions\AppException;
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

    #[Rule('required|string')]
    public string $identifier = '';

    /**
     * The user's password for login.
     */

    #[Rule('required|string')]
    public string $password = '';

    /**
     * Indicates whether the user should be remembered.
     */
    public bool $remember = false;

    /**
     * Define the validation rules for the component properties.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'identifier' => ['required', 'string', $this->isEmail($this->identifier) ? 'email' : null],
            'password' => 'required|string',
        ];
    }

    /**
     * Check if the given identifier string is likely an email address.
     *
     * @param  string  $value
     * @return bool
     */
    protected function isEmail(string $value): bool
    {
        return str_contains($value, '@');
    }

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
                'identifier' => $this->identifier,
                'password' => $this->password,
            ], $this->remember);

            $this->redirect(route('dashboard'), navigate: true);
        } catch (AppException $e) {
            $this->addError('identifier', $e->getUserMessage());
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
