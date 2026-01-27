<?php

namespace App\Middleware;

use System\Auth\Auth;
use App\Middleware\IMiddleware;

class AuthMiddleware implements IMiddleware
{
    /**
     * Handle the incoming request.
     * Redirects to login if user is not authenticated.
     * 
     * @return mixed
     */
    public function handle()
    {
        if (!Auth::checkLogin()) {
            redirect('/login');
        }
        
        return true;
    }
}