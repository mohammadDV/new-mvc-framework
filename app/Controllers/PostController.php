<?php

namespace App\Controllers;

use App\Enums\PostStatusType;
use App\Repositories\Contracts\IPostRepository;
use App\Requests\PostRequest;
use App\Exceptions\NotFoundException;
use App\Exceptions\DatabaseException;
use App\Services\UploadFile\ImageUpload;
use System\Auth\Auth;

/**
 * Post Controller
 * 
 * Handles HTTP requests for post CRUD operations.
 */
class PostController extends Controller
{

    /**
     * PostController constructor.
     * Injects the post repository via dependency injection.
     *
     * @param IPostRepository $postRepository
     */
    public function __construct(protected IPostRepository $postRepository)
    {
    }

    /**
     * Display a listing of posts.
     *
     * @return \System\View\View
     */
    public function index()
    {
        try {
            $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 15;
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            
            $paginator = $this->postRepository->paginate($perPage, $page);
            
            return view('app.post.index', compact('paginator'));
        } catch (DatabaseException $e) {
            redirect('/posts?error=Failed to load posts. Please try again later.');
        }
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \System\View\View
     */
    public function create()
    {
        return view('app.post.create');
    }

    /**
     * Store a newly created post in storage.
     *
     * @return void
     */
    public function store()
    {
        try {
            $request = new PostRequest();
            $data = $request->all();
            $data['user_id'] = Auth::user()['id'];
            $data['status'] = PostStatusType::from($data['status'])->value;
            $data['image'] = ImageUpload::UploadAndFitImage($request->file('image'));          

            $postId = $this->postRepository->create($data);
            
            if ($postId) {
                redirect('/posts?success=Post created successfully');
            } else {
                redirect('/posts/create?error=Failed to create post');
            }
        } catch (DatabaseException $e) {
            redirect('/posts/create?error=Failed to create post. Please try again.');
        } catch (\Exception $e) {
            redirect('/posts/create?error=An error occurred. Please try again.');
        }
    }

    /**
     * Display the specified post.
     *
     * @param int|string $id
     * @return \System\View\View|void
     */
    public function show($id)
    {
        try {
            $post = $this->postRepository->find($id);
            
            if (!$post) {
                throw new NotFoundException('Post not found');
            }

            return view('app.post.show', compact('post'));
        } catch (NotFoundException $e) {
            redirect('/posts?error=Post not found');
        } catch (DatabaseException $e) {
            redirect('/posts?error=Failed to load post. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified post.
     *
     * @param int|string $id
     * @return \System\View\View|void
     */
    public function edit($id)
    {
        try {
            $post = $this->postRepository->find($id);
            
            if (!$post) {
                throw new NotFoundException('Post not found');
            }

            return view('app.post.edit', compact('post'));
        } catch (NotFoundException $e) {
            redirect('/posts?error=Post not found');
        } catch (DatabaseException $e) {
            redirect('/posts?error=Failed to load post. Please try again.');
        }
    }

    /**
     * Update the specified post in storage.
     *
     * @param int|string $id
     * @return void
     */
    public function update($id)
    {
        try {
            $post = $this->postRepository->find($id);
            
            if (!$post) {
                throw new NotFoundException('Post not found');
            }

            $request = new PostRequest();
            $data = $request->all();
            $data['user_id'] = Auth::user()['id'];
            $data['status'] = PostStatusType::from($data['status'])->value;
            if (!empty($_GET['file']['name'])) {
                $data['image'] = ImageUpload::UploadAndFitImage($request->file('image'));          
            }

            $success = $this->postRepository->update($id, $data);
            
            if ($success) {
                redirect('/posts?success=Post updated successfully');
            } else {
                redirect("/posts/{$id}/edit?error=Failed to update post");
            }
        } catch (NotFoundException $e) {
            redirect('/posts?error=Post not found');
        } catch (DatabaseException $e) {
            redirect("/posts/{$id}/edit?error=Failed to update post. Please try again.");
        } catch (\Exception $e) {
            var_export($e);
            die();
            redirect("/posts/{$id}/edit?error=An error occurred. Please try again.");
        }
    }

    /**
     * Remove the specified post from storage.
     *
     * @param int|string $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            $post = $this->postRepository->find($id);
            
            if (!$post) {
                throw new NotFoundException('Post not found');
            }

            $success = $this->postRepository->delete($id);
            
            if ($success) {
                redirect('/posts?success=Post deleted successfully');
            } else {
                redirect('/posts?error=Failed to delete post');
            }
        } catch (NotFoundException $e) {
            redirect('/posts?error=Post not found');
        } catch (DatabaseException $e) {
            redirect('/posts?error=Failed to delete post. Please try again.');
        } catch (\Exception $e) {
            redirect('/posts?error=An error occurred. Please try again.');
        }
    }
}