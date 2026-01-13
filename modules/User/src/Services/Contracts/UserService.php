<?php

namespace Modules\User\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Models\User;

interface UserService
{
    /**
     * List users with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters  Filter criteria (e.g., 'search', 'sort').
     * @param  int  $perPage  Number of records per page.
     * @param  array<int, string>  $columns  Columns to retrieve.
     * @return LengthAwarePaginator Paginated list of users.
     */
    public function list(array $filters = [], int $perPage = 10, array $columns = ['*']): LengthAwarePaginator;

    /**
     * Create a new user record.
     *
     * @param  array<string, mixed>  $data  The data for creating the user.
     * @return User The newly created user.
     */
    public function create(array $data): User;

    /**
     * Find a user by their email address.
     *
     * @param  string  $email  The email address to search for.
     * @return User|null The found user or null.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by their username.
     *
     * @param  string  $username  The username to search for.
     * @return User|null The found user or null.
     */
    public function findByUsername(string $username): ?User;

    /**
     * Update a user's details by their ID.
     *
     * @param  mixed  $id  The UUID of the user.
     * @param  array<string, mixed>  $data  The data for updating the user.
     * @param  array<int, string>  $columns  Columns to retrieve after update.
     * @return User The updated user.
     */
    public function update(mixed $id, array $data, array $columns = ['*']): User;

    /**
     * Delete a user by their ID.
     *
     * @param  mixed  $id  The UUID of the user.
     * @return bool True if deletion was successful.
     */
    public function delete(mixed $id): bool;
}
