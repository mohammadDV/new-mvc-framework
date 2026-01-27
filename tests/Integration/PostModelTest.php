<?php

namespace Tests\Integration;

use Tests\DatabaseTestCase;
use App\Models\Post;
use App\Models\User;

/**
 * Integration Test for Post Model
 * 
 * Tests the Post model's database operations.
 */
class PostModelTest extends DatabaseTestCase
{
    /**
     * Test creating a new post.
     */
    public function test_can_create_post(): void
    {
        // First create a user (posts need a user_id)
        $userModel = new User();
        $userId = $userModel->createUser([
            'name' => 'Post Author',
            'email' => 'author@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        $postModel = new Post();
        
        $postData = [
            'title' => 'Test Post Title',
            'content' => 'This is the content of the test post.',
            'user_id' => $userId,
            'status' => 'published',
        ];
        
        $postId = $postModel->createPost($postData);
        
        // Assert post was created
        $this->assertIsNumeric($postId);
        $this->assertGreaterThan(0, $postId);
        
        // Assert post exists in database
        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post Title',
            'user_id' => $userId,
            'status' => 'published',
        ]);
        
        // Verify the post can be retrieved
        $post = $postModel->find($postId);
        $this->assertNotFalse($post);
        $this->assertEquals('Test Post Title', $post['title']);
        $this->assertEquals($userId, $post['user_id']);
    }

    /**
     * Test updating a post.
     */
    public function test_can_update_post(): void
    {
        // Create a user
        $userModel = new User();
        $userId = $userModel->createUser([
            'name' => 'Post Editor',
            'email' => 'editor@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        $postModel = new Post();
        
        // Create a post
        $postId = $postModel->createPost([
            'title' => 'Original Title',
            'content' => 'Original content',
            'user_id' => $userId,
            'status' => 'draft',
        ]);
        
        // Update the post
        $updateData = [
            'title' => 'Updated Title',
            'status' => 'published',
        ];
        
        $result = $postModel->updatePost($postId, $updateData);
        
        $this->assertTrue($result);
        
        // Verify the update
        $post = $postModel->find($postId);
        $this->assertEquals('Updated Title', $post['title']);
        $this->assertEquals('published', $post['status']);
        $this->assertEquals('Original content', $post['content']); // Should remain unchanged
    }

    /**
     * Test finding a post by ID.
     */
    public function test_can_find_post_by_id(): void
    {
        // Create a user
        $userModel = new User();
        $userId = $userModel->createUser([
            'name' => 'Post Finder',
            'email' => 'finder@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        $postModel = new Post();
        
        // Create a post
        $postId = $postModel->createPost([
            'title' => 'Findable Post',
            'content' => 'This post should be findable',
            'user_id' => $userId,
            'status' => 'published',
        ]);
        
        // Find the post
        $post = $postModel->findById($postId);
        
        $this->assertNotFalse($post);
        $this->assertEquals('Findable Post', $post['title']);
        $this->assertEquals($userId, $post['user_id']);
    }

    /**
     * Test that posts are associated with users.
     */
    public function test_post_belongs_to_user(): void
    {
        $userModel = new User();
        
        // Create two users
        $userId1 = $userModel->createUser([
            'name' => 'User One',
            'email' => 'user1@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        $userId2 = $userModel->createUser([
            'name' => 'User Two',
            'email' => 'user2@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        $postModel = new Post();
        
        // Create posts for each user
        $post1Id = $postModel->createPost([
            'title' => 'User One Post',
            'content' => 'Content',
            'user_id' => $userId1,
            'status' => 'published',
        ]);
        
        $post2Id = $postModel->createPost([
            'title' => 'User Two Post',
            'content' => 'Content',
            'user_id' => $userId2,
            'status' => 'published',
        ]);
        
        // Verify posts belong to correct users
        $post1 = $postModel->find($post1Id);
        $post2 = $postModel->find($post2Id);
        
        $this->assertEquals($userId1, $post1['user_id']);
        $this->assertEquals($userId2, $post2['user_id']);
        
        // Verify we can find all posts by user_id
        $user1Posts = $postModel->findAllBy('user_id', $userId1);
        $this->assertCount(1, $user1Posts);
        $this->assertEquals('User One Post', $user1Posts[0]['title']);
    }
}