<?php

namespace Modules\User\Contracts\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\User\Models\User;

interface UserService
{
    /**
     * List users with optional filtering and pagination.
     *
     * @param  array<string, mixed>  $filters
     */
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new user.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User;

    /**
     * Find a user by ID.
     */
    public function findById(string $id): ?User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by username.
     */
    public function findByUsername(string $username): ?User;

    /**
     * Update a user's details.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(string $id, array $data): User;

    /**
     * Delete a user.
     */
    public function delete(string $id): bool;
}
