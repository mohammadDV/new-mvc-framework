<?php

namespace App\Models;

/**
 * User Model
 * 
 * Handles all database operations related to users.
 */
class Post extends Model
{
    /**
     * The name of the database table associated with the model.
     *
     * @var string
     */
    protected string $table = 'posts';

    /**
     * The primary key for the model's table.
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [
        'title',
        'content',
        'user_id',
        'image',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Find a post by title.
     *
     * @param string $email
     * @return array|false
     */
    public function findById(int $id)
    {
        return $this->find($id);
    }

    /**
     * Create a new post.
     *
     * @param array $data
     * @return int|string The ID of the newly created post
     */
    public function createPost(array $data)
    {
        // Add timestamps if not provided
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        return $this->create($data);
    }

    /**
     * Update post information.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function updatePost($id, array $data): bool
    {
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->update($id, $data);
    }
}