<?php

namespace Modules\User\Contracts\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\User\Models\User;

interface AuthService
{
    /**
     * Attempt to log in a user with the given credentials.
     *
     * @param  array  $credentials  Contains 'email' (which can be an email or username), 'password'.
     * @param  bool  $remember  Whether to "remember" the user.
     * @return Authenticatable|User The authenticated user.
     *
     * @throws \Modules\Shared\Exceptions\AppException If authentication fails.
     */
    public function login(array $credentials, bool $remember = false): Authenticatable|User;

    /**
     * Log out the currently authenticated user.
     */
    public function logout(): void;

    /**
     * Register a new user.
     *
     * @param  array  $data  Contains user data including 'name', 'email', 'password'.
     * @return User The newly registered user.
     *
     * @throws \Modules\Shared\Exceptions\AppException If registration fails (e.g., duplicate email).
     */
    public function register(array $data): User;

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|User|null The authenticated user, or null if no user is authenticated.
     */
    public function getAuthenticatedUser(): Authenticatable|User|null;

    /**
     * Change the password for a user.
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool;

    /**
     * Send the password reset link to a user.
     */
    public function sendPasswordResetLink(string $email): void;

    /**
     * Reset the password for a user.
     */
    public function resetPassword(array $credentials): bool;

    /**
     * Verify a user's email address.
     */
    public function verifyEmail(string $id, string $hash): bool;

    /**
     * Resend the email verification notification.
     */
    public function resendVerificationEmail(User $user): void;

    /**
     * Confirm a user's password.
     */
    public function confirmPassword(User $user, string $password): bool;
}
