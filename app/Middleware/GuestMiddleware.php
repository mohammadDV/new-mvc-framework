<?php

namespace App\Middleware;

use System\Auth\Auth;
use App\Middleware\IMiddleware;

class GuestMiddleware implements IMiddleware
{
    /**
     * Handle the incoming request.
     * Redirects to home if user is already authenticated.
     * 
     * @return mixed
     */
    public function handle()
    {
        if (Auth::checkLogin()) {
            redirect('/');
        }
        
        return true;
    }
}