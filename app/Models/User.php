<?php

namespace App\Models;

/**
 * User Model
 * 
 * Handles all database operations related to users.
 */
class User extends Model
{
    /**
     * The name of the database table associated with the model.
     *
     * @var string
     */
    protected string $table = 'users';

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
        'name',
        'email',
        'password',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden from serialization.
     *
     * @var array
     */
    protected array $hidden = [
        'password',
    ];

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email)
    {
        return $this->findBy('email', $email);
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return int|string The ID of the newly created user
     */
    public function createUser(array $data)
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
     * Update user information.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function updateUser($id, array $data): bool
    {
        // Update timestamp
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->update($id, $data);
    }

    /**
     * Find or create a user by email.
     * Useful for login scenarios.
     *
     * @param string $email
     * @param array $additionalData
     * @return array
     */
    public function findOrCreateByEmail(string $email, array $additionalData = []): array
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            $data = array_merge(['email' => $email], $additionalData);
            $userId = $this->createUser($data);
            $user = $this->find($userId);
        }

        return $user;
    }

    /**
     * Verify user password.
     *
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function verifyPassword(string $email, string $password)
    {
        $user = $this->findByEmail($email);

        if ($user && isset($user['password'])) {
            if (password_verify($password, $user['password'])) {
                return $this->hideAttributes($user);
            }
        }

        return false;
    }

    /**
     * Hash password before storing.
     *
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Hide specified attributes from the user data.
     *
     * @param array $data
     * @return array
     */
    public function hideUserAttributes(array $data): array
    {
        return $this->hideAttributes($data);
    }
}