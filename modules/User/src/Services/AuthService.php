<?php

namespace Modules\User\Services;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService as AuthServiceContract;
use Modules\User\Models\User;

class AuthService implements AuthServiceContract
{
    /**
     * Attempt to log in a user with the given credentials.
     *
     * @param array $credentials Contains 'email' and 'password'.
     * @return Authenticatable|User The authenticated user.
     * @throws AppException If authentication fails.
     */
    public function login(array $credentials): Authenticatable|User
    {
        if (!Auth::attempt($credentials)) {
            throw new AppException(
                userMessage: 'Invalid credentials.',
                logMessage: 'Authentication attempt failed for email: ' . ($credentials['email'] ?? 'N/A'),
                code: 401
            );
        }

        return Auth::user();
    }

    /**
     * Log out the currently authenticated user.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Register a new user.
     *
     * @param array $data Contains user data including 'name', 'email', 'password'.
     * @return User The newly registered user.
     * @throws AppException If registration fails (e.g., duplicate email).
     */
    public function register(array $data): User
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            return $user;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry SQLSTATE code
                throw new AppException(
                    userMessage: 'A user with this email already exists.',
                    logMessage: 'Attempted to register with duplicate email: ' . $data['email'],
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'An error occurred during registration.',
                logMessage: 'Registration failed due to database error: ' . $e->getMessage(),
                code: 500,
                previous: $e
            );
        }
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|User|null The authenticated user, or null if no user is authenticated.
     */
    public function getAuthenticatedUser(): Authenticatable|User|null
    {
        return Auth::user();
    }
}
