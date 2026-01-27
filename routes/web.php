<?php

use System\Router\Web\Route;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;
    
// Home Routes
Route::get("/", "HomeController@index", "home.index");
Route::get("/profile", "HomeController@profile", "home.profile", [AuthMiddleware::class]);

// Authentication Routes (should only be accessible to guests)
Route::get('/login', 'AuthController@showLogin', 'auth.login', [GuestMiddleware::class]);
Route::post('/login', 'AuthController@login', 'auth.login.post', [GuestMiddleware::class, CsrfMiddleware::class]);
Route::get('/register', 'AuthController@showRegister', 'auth.register', [GuestMiddleware::class]);
Route::post('/register', 'AuthController@register', 'auth.register.post', [GuestMiddleware::class, CsrfMiddleware::class]);
Route::post('/logout', 'AuthController@logout', 'auth.logout', [AuthMiddleware::class, CsrfMiddleware::class]);
Route::get('/logout', 'AuthController@logout', 'auth.logout.get', [AuthMiddleware::class]);

// User CRUD Routes (protected)
Route::get('/users', 'UserController@index', 'users.index', [AuthMiddleware::class]);
Route::get('/users/create', 'UserController@create', 'users.create', [AuthMiddleware::class]);
Route::post('/users', 'UserController@store', 'users.store', [AuthMiddleware::class, CsrfMiddleware::class]);
Route::get('/users/{id}', 'UserController@show', 'users.show', [AuthMiddleware::class]);
Route::get('/users/{id}/edit', 'UserController@edit', 'users.edit', [AuthMiddleware::class]);
Route::post('/users/{id}/update', 'UserController@update', 'users.update', [AuthMiddleware::class, CsrfMiddleware::class]);
Route::post('/users/{id}/delete', 'UserController@destroy', 'users.destroy', [AuthMiddleware::class, CsrfMiddleware::class]);

// Posts CRUD Routes (protected)
Route::get('/posts', 'PostController@index', 'posts.index', [AuthMiddleware::class]);
Route::get('/posts/create', 'PostController@create', 'posts.create', [AuthMiddleware::class]);
Route::post('/posts', 'PostController@store', 'posts.store', [AuthMiddleware::class, CsrfMiddleware::class]);
Route::get('/posts/{id}', 'PostController@show', 'posts.show');
Route::get('/posts/{id}/edit', 'PostController@edit', 'posts.edit', [AuthMiddleware::class]);
Route::post('/posts/{id}/update', 'PostController@update', 'posts.update', [AuthMiddleware::class, CsrfMiddleware::class]);
Route::post('/posts/{id}/delete', 'PostController@destroy', 'posts.destroy', [AuthMiddleware::class, CsrfMiddleware::class]);