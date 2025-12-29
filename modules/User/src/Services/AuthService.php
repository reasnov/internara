<?php

namespace Modules\User\Services;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService as AuthServiceContract;
use Modules\User\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Verified;

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
                'password' => $data['password'],
            ]);

            event(new Registered($user));

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

    /**
     * Change the password for a user.
     *
     * @param User $user
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     * @throws AppException
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new AppException(
                userMessage: 'The provided current password does not match our records.',
                code: 422
            );
        }

        $user->password = Hash::make($newPassword);
        return $user->save();
    }

    /**
     * Send the password reset link to a user.
     *
     * @param string $email
     * @return void
     */
    public function sendPasswordResetLink(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset the password for a user.
     *
     * @param array $credentials
     * @return bool
     */
    public function resetPassword(array $credentials): bool
    {
        $response = Password::reset($credentials, function (User $user, string $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        return $response == Password::PASSWORD_RESET;
    }

    /**
     * Verify a user's email address.
     *
     * @param string $id
     * @param string $hash
     * @return bool
     */
    public function verifyEmail(string $id, string $hash): bool
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return false;
        }

        if ($user->hasVerifiedEmail()) {
            return true;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            return true;
        }

        return false;
    }

    /**
     * Resend the email verification notification.
     *
     * @param User $user
     * @return void
     */
    public function resendVerificationEmail(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new AppException(
                userMessage: 'This email address is already verified.',
                code: 422
            );
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * Confirm a user's password.
     *
     * @param User $user
     * @param string $password
     * @return bool
     */
    public function confirmPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
