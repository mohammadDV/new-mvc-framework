<?php

namespace App\Repositories;

use App\Models\Post;
use App\Support\Paginator;
use App\Repositories\Contracts\IPostRepository;

/**
 * Post Repository
 * 
 * Handles all database operations for posts using the repository pattern.
 */
class PostRepository implements IPostRepository
{
    /**
     * The Post model instance.
     *
     * @var Post
     */
    protected Post $post;

    /**
     * PostRepository constructor.
     * Initializes the Post model.
     */
    public function __construct()
    {
        $this->post = new Post();
    }

    /**
     * Get all posts.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->post->all();
    }

    /**
     * Find a post by ID.
     *
     * @param int|string $id
     * @return array
     */
    public function find($id)
    {
        return $this->post->find($id);
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return int|string The ID of the newly created post
     */
    public function create(array $data)
    {
        return $this->post->createPost($data);
    }

    /**
     * Update a post.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        return $this->post->updatePost($id, $data);
    }

    /**
     * Delete a post.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->post->delete($id);
    }

    /**
     * Get paginated posts.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return Paginator
     */
    public function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        $paginationData = $this->post->paginate($perPage, $page);

        return new Paginator(
            $paginationData['items'],
            $paginationData['total'],
            $paginationData['perPage'],
            $paginationData['currentPage']
        );
    }

    /**
     * Get paginated posts with user information (avoids N+1 queries).
     * Uses a JOIN query to fetch posts and user names in a single query.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return Paginator
     */
    public function paginateWithUser(int $perPage = 15, int $page = 1): Paginator
    {
        // Ensure valid page number
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $perPage = (int) $perPage;
        $offset = (int) $offset;

        // Get total count of posts
        $postsTable = $this->post->getTable();
        $countQuery = "SELECT COUNT(*) as count FROM {$postsTable}";
        $totalResult = $this->post->queryOne($countQuery);
        $total = (int) ($totalResult['count'] ?? 0);

        // Get paginated posts with user names using JOIN
        // Order by latest first (created_at DESC)
        $query = "
            SELECT 
                p.id,
                p.title,
                p.content,
                p.user_id,
                p.image,
                p.status,
                p.created_at,
                p.updated_at,
                u.name as user_name
            FROM {$postsTable} p
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.status = 'published'
            ORDER BY p.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";

        $items = $this->post->query($query);

        return new Paginator(
            $items,
            $total,
            $perPage,
            $page
        );
    }
}