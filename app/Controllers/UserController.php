<?php

namespace App\Controllers;

use App\Repositories\Contracts\IUserRepository;
use App\Requests\UserRequest;
use App\Exceptions\NotFoundException;
use App\Exceptions\DatabaseException;

/**
 * User Controller
 * 
 * Handles HTTP requests for user CRUD operations.
 */
class UserController extends Controller
{

    /**
     * UserController constructor.
     * Injects the user repository via dependency injection.
     *
     * @param IUserRepository $userRepository
     */
    public function __construct(protected IUserRepository $userRepository)
    {
    }

    /**
     * Display a listing of users.
     *
     * @return \System\View\View
     */
    public function index()
    {
        try {
            $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 15;
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            
            $paginator = $this->userRepository->paginate($perPage, $page);
            
            return view('app.user.index', compact('paginator'));
        } catch (DatabaseException $e) {
            redirect('/users?error=Failed to load users. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \System\View\View
     */
    public function create()
    {
        return view('app.user.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @return void
     */
    public function store()
    {
        try {
            $request = new UserRequest();
            $data = $request->all();

            $userId = $this->userRepository->create($data);
            
            if ($userId) {
                redirect('/users?success=User created successfully');
            } else {
                redirect('/users/create?error=Failed to create user');
            }
        } catch (DatabaseException $e) {
            redirect('/users/create?error=Failed to create user. Please try again.');
        } catch (\Exception $e) {
            redirect('/users/create?error=An error occurred. Please try again.');
        }
    }

    /**
     * Display the specified user.
     *
     * @param int|string $id
     * @return \System\View\View|void
     */
    public function show($id)
    {
        try {
            $user = $this->userRepository->find($id);
            
            if (!$user) {
                throw new NotFoundException('User not found');
            }

            return view('app.user.show', compact('user'));
        } catch (NotFoundException $e) {
            redirect('/users?error=User not found');
        } catch (DatabaseException $e) {
            redirect('/users?error=Failed to load user. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param int|string $id
     * @return \System\View\View|void
     */
    public function edit($id)
    {
        try {
            $user = $this->userRepository->find($id);
            
            if (!$user) {
                throw new NotFoundException('User not found');
            }

            return view('app.user.edit', compact('user'));
        } catch (NotFoundException $e) {
            redirect('/users?error=User not found');
        } catch (DatabaseException $e) {
            redirect('/users?error=Failed to load user. Please try again.');
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param int|string $id
     * @return void
     */
    public function update($id)
    {
        try {
            $user = $this->userRepository->find($id);
            
            if (!$user) {
                throw new NotFoundException('User not found');
            }

            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
            ];

            // Only update password if provided
            if (!empty($_POST['password'])) {
                $data['password'] = $_POST['password'];
            }

            // Basic validation
            if (empty($data['name']) || empty($data['email'])) {
                redirect("/users/{$id}/edit?error=Name and email are required");
                return;
            }

            // Check if email already exists for another user
            $existingUser = $this->userRepository->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $id) {
                redirect("/users/{$id}/edit?error=Email already exists");
                return;
            }

            $success = $this->userRepository->update($id, $data);
            
            if ($success) {
                redirect('/users?success=User updated successfully');
            } else {
                redirect("/users/{$id}/edit?error=Failed to update user");
            }
        } catch (NotFoundException $e) {
            redirect('/users?error=User not found');
        } catch (DatabaseException $e) {
            redirect("/users/{$id}/edit?error=Failed to update user. Please try again.");
        } catch (\Exception $e) {
            redirect("/users/{$id}/edit?error=An error occurred. Please try again.");
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param int|string $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            $user = $this->userRepository->find($id);
            
            if (!$user) {
                throw new NotFoundException('User not found');
            }

            $success = $this->userRepository->delete($id);
            
            if ($success) {
                redirect('/users?success=User deleted successfully');
            } else {
                redirect('/users?error=Failed to delete user');
            }
        } catch (NotFoundException $e) {
            redirect('/users?error=User not found');
        } catch (DatabaseException $e) {
            redirect('/users?error=Failed to delete user. Please try again.');
        } catch (\Exception $e) {
            redirect('/users?error=An error occurred. Please try again.');
        }
    }
}