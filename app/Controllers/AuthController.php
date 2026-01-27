<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Contracts\IUserRepository;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use System\Auth\Auth;
use System\Session\Session;
use App\Exceptions\DatabaseException;

/**
 * Authentication Controller
 * 
 * Handles user authentication operations including login, logout, and registration.
 */
class AuthController extends Controller
{
    /**
     * AuthController constructor.
     * Injects the user repository via dependency injection.
     *
     * @param IUserRepository $userRepository
     */
    public function __construct(protected IUserRepository $userRepository)
    {
    }

    /**
     * Show the login form.
     *
     * @return \System\View\View|void
     */
    public function showLogin()
    {
        // Redirect if already logged in
        if (Auth::checkLogin()) {
            redirect('/');
            return;
        }

        return view('app.auth.login');
    }

    /**
     * Handle user login request.
     *
     * @return void
     */
    public function login(): void
    {
        // Redirect if already logged in
        if (Auth::checkLogin()) {
            redirect('/');
            return;
        }

        // Validate request
        $request = new LoginRequest();
        $data = $request->all();

        // Verify credentials
        $user = $this->userRepository->verifyCredentials(
            $data['email'],
            $data['password']
        );

        if (!$user) {
            error('login', 'Invalid email or password');
            redirect('/login');
            return;
        }

        // Store user in session
        Session::set('user', $user);

        // Set success flash message
        flash('login', 'Welcome back! You have been logged in successfully.');

        // Redirect to home or intended page
        redirect('/');
    }

    /**
     * Handle user logout request.
     *
     * @return void
     */
    public function logout(): void
    {
        // Logout using Auth class
        Auth::logout();

        // Regenerate session ID for security
        if (session_status() === \PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        flash('logout', 'You have been logged out successfully.');
        redirect('/login');
    }

    /**
     * Show the registration form.
     *
     * @return \System\View\View|void
     */
    public function showRegister()
    {
        // Redirect if already logged in
        if (Auth::checkLogin()) {
            redirect('/');
            return;
        }

        return view('app.auth.register');
    }

    /**
     * Handle user registration request.
     *
     * @return void
     */
    public function register(): void
    {
        try {
            // Redirect if already logged in
            if (Auth::checkLogin()) {
                redirect('/');
                return;
            }

            // Validate request
            $request = new RegisterRequest();
            $data = $request->all();

            // Remove confirm_password from data as it's not needed in database
            unset($data['confirm_password']);

            // Create user
            $userId = $this->userRepository->create($data);

            if (!$userId) {
                error('register', 'Registration failed. Please try again.');
                redirect('/register');
                return;
            }

            // Automatically log in the newly registered user
            $user = $this->userRepository->find($userId);
            if ($user) {
                Session::set('user', $user);
                flash('register', 'Registration successful. Welcome!');
                redirect('/');
                return;
            }

            flash('register', 'Registration successful. Please login.');
            redirect('/login');
        } catch (DatabaseException $e) {
            error('register', 'Registration failed due to a database error. Please try again.');
            redirect('/register');
        } catch (\Exception $e) {
            error('register', 'An unexpected error occurred. Please try again.');
            redirect('/register');
        }
    }
}