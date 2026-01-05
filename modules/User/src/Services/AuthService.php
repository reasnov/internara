<?php

namespace Modules\User\Services;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Modules\Shared\Exceptions\AppException;
use Modules\User\Contracts\Services\AuthService as AuthServiceContract;
use Modules\User\Models\User;

class AuthService implements AuthServiceContract
{
    /**
     * Attempt to log in a user with the given credentials.
     *
     * @param  array  $credentials  Contains 'email' (which can be an email or username), 'password'.
     * @param  bool  $remember  Whether to "remember" the user.
     * @return Authenticatable|User The authenticated user.
     *
     * @throws AppException If authentication fails.
     */
    public function login(array $credentials, bool $remember = false): Authenticatable|User
    {
        // The 'email' field from the form can be either an email or a username.
        $identifier = $credentials['email'];

        // Determine if the identifier is an email or a username.
        $loginField = Str::contains($identifier, '@') ? 'email' : 'username';

        $authCredentials = [
            $loginField => $identifier,
            'password' => $credentials['password'],
        ];

        if (! Auth::attempt($authCredentials, $remember)) {
            throw new AppException(
                userMessage: 'user::exceptions.invalid_credentials',
                logMessage: 'Authentication attempt failed for: '.$identifier,
                code: 401
            );
        }

        return Auth::user();
    }

    /**
     * Log out the currently authenticated user.
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * Register a new user.
     *
     * @param  array  $data  Contains user data including 'name', 'email', 'password'.
     * @return User The newly registered user.
     *
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

            event(new Registered($user));

            return $user;
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') { // Duplicate entry SQLSTATE code
                throw new AppException(
                    userMessage: 'shared::exceptions.email_exists',
                    replace: ['record' => 'pengguna'],
                    logMessage: 'Attempted to register with duplicate email: '.$data['email'],
                    code: 409, // Conflict
                    previous: $e
                );
            }
            throw new AppException(
                userMessage: 'shared::exceptions.creation_failed',
                replace: ['record' => 'pengguna'],
                logMessage: 'Registration failed due to database error: '.$e->getMessage(),
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
     * @throws AppException
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (! Hash::check($currentPassword, $user->password)) {
            throw new AppException(
                userMessage: 'user::exceptions.password_mismatch',
                code: 422
            );
        }

        $user->password = Hash::make($newPassword);

        return $user->save();
    }

    /**
     * Send the password reset link to a user.
     */
    public function sendPasswordResetLink(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }

    /**
     * Reset the password for a user.
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
     */
    public function verifyEmail(string $id, string $hash): bool
    {
        $user = User::findOrFail($id);

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
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
     */
    public function resendVerificationEmail(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            throw new AppException(
                userMessage: 'user::exceptions.email_already_verified',
                code: 422
            );
        }

        $user->sendEmailVerificationNotification();
    }

    /**
     * Confirm a user's password.
     */
    public function confirmPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}
