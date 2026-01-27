<?php

namespace App\Repositories\Contracts;

use App\Support\Paginator;

/**
 * Post Repository Interface
 * 
 * Defines the contract for post data operations.
 */
interface IPostRepository
{
    /**
     * Get all posts.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Find a post by ID.
     *
     * @param int|string $id
     * @return array|false
     */
    public function find($id);

    /**
     * Create a new post.
     *
     * @param array $data
     * @return int|string The ID of the newly created post
     */
    public function create(array $data);

    /**
     * Update a post.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool;

    /**
     * Delete a post.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool;

    /**
     * Get paginated posts.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return \App\Support\Paginator
     */
    public function paginate(int $perPage = 15, int $page = 1): Paginator;

    /**
     * Get paginated posts with user information (avoids N+1 queries).
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return \App\Support\Paginator
     */
    public function paginateWithUser(int $perPage = 15, int $page = 1): Paginator;
    
}