<?php

namespace App\Repositories;

use App\Models\User;
use App\Support\Paginator;
use App\Repositories\Contracts\IUserRepository;

/**
 * User Repository
 * 
 * Handles all database operations for users using the repository pattern.
 */
class UserRepository implements IUserRepository
{
    /**
     * The User model instance.
     *
     * @var User
     */
    protected User $user;

    /**
     * UserRepository constructor.
     * Initializes the User model.
     */
    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Get all users.
     *
     * @return array
     */
    public function all(): array
    {
        $users = $this->user->all();
        return array_map(function ($user) {
            return $this->user->hideUserAttributes($user);
        }, $users);
    }

    /**
     * Find a user by ID.
     *
     * @param int|string $id
     * @return array|false
     */
    public function find($id)
    {
        $user = $this->user->find($id);
        if ($user) {
            return $this->user->hideUserAttributes($user);
        }
        return false;
    }

    /**
     * Find a user by email.
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email)
    {
        $user = $this->user->findByEmail($email);
        if ($user) {
            return $this->user->hideUserAttributes($user);
        }
        return false;
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return int|string The ID of the newly created user
     */
    public function create(array $data)
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = $this->user->hashPassword($data['password']);
        }

        return $this->user->createUser($data);
    }

    /**
     * Update a user.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data): bool
    {
        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = $this->user->hashPassword($data['password']);
        } else {
            // Remove password from update if empty
            unset($data['password']);
        }

        return $this->user->updateUser($id, $data);
    }

    /**
     * Delete a user.
     *
     * @param int|string $id
     * @return bool
     */
    public function delete($id): bool
    {
        return $this->user->delete($id);
    }

    /**
     * Find or create a user by email.
     *
     * @param string $email
     * @param array $additionalData
     * @return array
     */
    public function findOrCreateByEmail(string $email, array $additionalData = []): array
    {
        $user = $this->user->findOrCreateByEmail($email, $additionalData);
        return $this->user->hideUserAttributes($user);
    }

    /**
     * Get paginated users.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return Paginator
     */
    public function paginate(int $perPage = 15, int $page = 1): Paginator
    {
        $paginationData = $this->user->paginate($perPage, $page);
        
        // Hide attributes from each user
        $items = array_map(function ($user) {
            return $this->user->hideUserAttributes($user);
        }, $paginationData['items']);

        return new Paginator(
            $items,
            $paginationData['total'],
            $paginationData['perPage'],
            $paginationData['currentPage']
        );
    }

    /**
     * Verify user credentials for authentication.
     *
     * @param string $email
     * @param string $password
     * @return array|false Returns user data without password on success, false on failure
     */
    public function verifyCredentials(string $email, string $password)
    {
        $user = $this->user->verifyPassword($email, $password);
        
        if ($user) {
            return $this->user->hideUserAttributes($user);
        }
        
        return false;
    }
}