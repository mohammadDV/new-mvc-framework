<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Services\Csrf\CsrfToken;
use App\Middleware\IMiddleware;

/**
 * CSRF Protection Middleware
 * Validates CSRF tokens on state-changing HTTP methods (POST, PUT, DELETE)
 */
class CsrfMiddleware implements IMiddleware
{
    /**
     * HTTP methods that require CSRF protection
     */
    private const PROTECTED_METHODS = ['POST', 'PUT', 'DELETE', 'PATCH'];

    /**
     * Validates CSRF token for state-changing requests.
     * 
     * @return mixed Return true to continue, or redirect/response to stop
     */
    public function handle()
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Only validate CSRF for state-changing methods
        if (!in_array(strtoupper($method), self::PROTECTED_METHODS, true)) {
            return true;
        }

        // Get token from request (check both POST and headers)
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        // Validate the token
        if (!CsrfToken::validate($token)) {
            // Regenerate token to prevent token fixation attacks
            CsrfToken::regenerate();
            
            // Redirect back with error
            error('csrf', 'Invalid security token. Please try again.');
            back();
        }

        // Token is valid, regenerate it for next request (optional but more secure)
        CsrfToken::regenerate();

        return true;
    }
}