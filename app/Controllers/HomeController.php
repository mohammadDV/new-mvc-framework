<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\Contracts\IPostRepository;
use System\Auth\Auth;

/**
 * Home Controller
 * 
 * Handles home page operations.
 */
class HomeController extends Controller
{
    
    /**
     * HomeController constructor.
     * Injects the post repository via dependency injection.
     *
     * @param IPostRepository $userRepository
     */
    public function __construct(protected IPostRepository $postRepository)
    {
    }

    /**
     * Show the home page.
     *
     * @return \System\View\View|void
     */
    public function index()
    {

        $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 15;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

        // Use paginateWithUser to avoid N+1 queries and get latest posts with user names
        $posts = $this->postRepository->paginateWithUser($perPage, $page);
            
        return view('app.index', compact('posts'));
    }

    /**
     * Show the profile page.
     *
     * @return \System\View\View|void
     */
    public function profile()
    {
        $user = Auth::user();
        return view('app.profile', compact('user'));
    }
}