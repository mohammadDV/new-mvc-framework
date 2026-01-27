<?php

declare(strict_types=1);

namespace App\Services\Csrf;

use System\Session\Session;

/**
 * CSRF Token Service
 * Handles generation, validation, and management of CSRF tokens
 */
class CsrfToken
{
    /**
     * Session key for storing CSRF token
     */
    private const SESSION_KEY = '_csrf_token';

    /**
     * Generate a new CSRF token and store it in session
     * 
     * @return string The generated token
     */
    public static function generate(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }

    /**
     * Get the current CSRF token from session, or generate a new one if it doesn't exist
     * 
     * @return string The CSRF token
     */
    public static function get(): string
    {
        $token = Session::get(self::SESSION_KEY);
        
        if (!$token) {
            $token = self::generate();
        }
        
        return $token;
    }

    /**
     * Validate a CSRF token against the one stored in session
     * 
     * @param string|null $token The token to validate
     * @return bool True if valid, false otherwise
     */
    public static function validate(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $sessionToken = Session::get(self::SESSION_KEY);
        
        if (!$sessionToken) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Regenerate the CSRF token (useful after successful validation)
     * 
     * @return string The new token
     */
    public static function regenerate(): string
    {
        return self::generate();
    }
}