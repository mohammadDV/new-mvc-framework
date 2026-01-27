<?php

namespace App\Middleware;

interface IMiddleware
{
    /**
     * Handle the incoming request.
     * 
     * @return mixed Return true to continue, or redirect/response to stop
     */
    public function handle();
}