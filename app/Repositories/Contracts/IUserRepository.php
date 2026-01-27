<?php

namespace App\Repositories\Contracts;

use App\Support\Paginator;

/**
 * User Repository Interface
 * 
 * Defines the contract for user data operations.
 */
interface IUserRepository
{
    /**
     * Get all users.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Find a user by ID.
     *
     * @param int|string $id
     * @return array|false
     */
    public function find($id);

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email);

    /**
     * Create a new user.
     *
     * @param array $data
     * @return int|string The ID of the newly created user
     */
    public function create(array $data);

    /**
     * Update a user.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool;

    /**
     * Delete a user.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool;

    /**
     * Find or create a user by email.
     *
     * @param string $email
     * @param array $additionalData
     * @return array
     */
    public function findOrCreateByEmail(string $email, array $additionalData = []): array;

    /**
     * Get paginated users.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return \App\Support\Paginator
     */
    public function paginate(int $perPage = 15, int $page = 1): Paginator;

    /**
     * Verify user credentials for authentication.
     *
     * @param string $email
     * @param string $password
     * @return array|false Returns user data without password on success, false on failure
     */
    public function verifyCredentials(string $email, string $password);
}