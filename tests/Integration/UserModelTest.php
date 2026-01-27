<?php

namespace Tests\Integration;

use Tests\DatabaseTestCase;
use App\Models\User;

/**
 * Integration Test for User Model
 * 
 * Tests the User model's database operations.
 */
class UserModelTest extends DatabaseTestCase
{
    /**
     * Test creating a new user.
     */
    public function test_can_create_user(): void
    {
        $userModel = new User();
        
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ];
        
        $userId = $userModel->createUser($userData);
        
        // Assert user was created
        $this->assertIsNumeric($userId);
        $this->assertGreaterThan(0, $userId);
        
        // Assert user exists in database
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);
        
        // Verify the user can be retrieved
        $user = $userModel->find($userId);
        $this->assertNotFalse($user);
        $this->assertEquals('John Doe', $user['name']);
        $this->assertEquals('john@example.com', $user['email']);
    }

    /**
     * Test finding a user by email.
     */
    public function test_can_find_user_by_email(): void
    {
        $userModel = new User();
        
        // Create a user first
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ];
        
        $userModel->createUser($userData);
        
        // Find the user by email
        $user = $userModel->findByEmail('jane@example.com');
        
        $this->assertNotFalse($user);
        $this->assertEquals('Jane Doe', $user['name']);
        $this->assertEquals('jane@example.com', $user['email']);
    }

    /**
     * Test updating a user.
     */
    public function test_can_update_user(): void
    {
        $userModel = new User();
        
        // Create a user first
        $userData = [
            'name' => 'Original Name',
            'email' => 'update@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ];
        
        $userId = $userModel->createUser($userData);
        
        // Update the user
        $updateData = [
            'name' => 'Updated Name',
        ];
        
        $result = $userModel->updateUser($userId, $updateData);
        
        $this->assertTrue($result);
        
        // Verify the update
        $user = $userModel->find($userId);
        $this->assertEquals('Updated Name', $user['name']);
        $this->assertEquals('update@example.com', $user['email']);
        
        // Assert database has the updated record
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * Test password verification.
     */
    public function test_can_verify_password(): void
    {
        $userModel = new User();
        
        $password = 'securepassword123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Create a user
        $userData = [
            'name' => 'Test User',
            'email' => 'password@example.com',
            'password' => $hashedPassword,
        ];
        
        $userModel->createUser($userData);
        
        // Verify correct password
        $user = $userModel->verifyPassword('password@example.com', $password);
        $this->assertNotFalse($user);
        $this->assertEquals('Test User', $user['name']);
        $this->assertArrayNotHasKey('password', $user); // Password should be hidden
        
        // Verify incorrect password
        $user = $userModel->verifyPassword('password@example.com', 'wrongpassword');
        $this->assertFalse($user);
    }

    /**
     * Test find or create by email.
     */
    public function test_find_or_create_by_email(): void
    {
        $userModel = new User();
        
        // First call should create the user
        $user = $userModel->findOrCreateByEmail('newuser@example.com', [
            'name' => 'New User',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        $this->assertIsArray($user);
        $this->assertEquals('New User', $user['name']);
        $this->assertEquals('newuser@example.com', $user['email']);
        
        // Second call should find the existing user
        $user2 = $userModel->findOrCreateByEmail('newuser@example.com', [
            'name' => 'Different Name', // This should be ignored
        ]);
        
        $this->assertEquals($user['id'], $user2['id']);
        $this->assertEquals('New User', $user2['name']); // Original name should remain
    }

    /**
     * Test that hidden attributes are not returned.
     */
    public function test_hides_password_attribute(): void
    {
        $userModel = new User();
        
        $userData = [
            'name' => 'Hidden Password User',
            'email' => 'hidden@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ];
        
        $userId = $userModel->createUser($userData);
        
        // Direct find should include password
        $user = $userModel->find($userId);
        $this->assertArrayHasKey('password', $user);
        
        // Using hideUserAttributes should remove password
        $hiddenUser = $userModel->hideUserAttributes($user);
        $this->assertArrayNotHasKey('password', $hiddenUser);
    }

    /**
     * Test counting users.
     */
    public function test_can_count_users(): void
    {
        $userModel = new User();
        
        // Initially should be 0 (transaction is isolated)
        $initialCount = $userModel->count();
        $this->assertEquals(0, $initialCount);
        
        // Create some users
        $userModel->createUser([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        $userModel->createUser([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);
        
        // Count should be 2
        $this->assertDatabaseCount('users', 2);
        $this->assertEquals(2, $userModel->count());
    }
}
