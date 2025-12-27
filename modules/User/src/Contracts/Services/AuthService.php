<?php

namespace Modules\User\Contracts\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\User\Models\User;

interface AuthService
{
    /**
     * Attempt to log in a user with the given credentials.
     *
     * @param array $credentials Contains 'email' and 'password'.
     * @return Authenticatable|User The authenticated user.
     * @throws \Modules\Shared\Exceptions\AppException If authentication fails.
     */
    public function login(array $credentials): Authenticatable|User;

    /**
     * Log out the currently authenticated user.
     *
     * @return void
     */
    public function logout(): void;

    /**
     * Register a new user.
     *
     * @param array $data Contains user data including 'name', 'email', 'password'.
     * @return User The newly registered user.
     * @throws \Modules\Shared\Exceptions\AppException If registration fails (e.g., duplicate email).
     */
    public function register(array $data): User;

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|User|null The authenticated user, or null if no user is authenticated.
     */
    public function getAuthenticatedUser(): Authenticatable|User|null;
}
