<?php

declare(strict_types=1);

namespace App\Requests;

use System\Request\Request;

/**
 * Login Request
 * 
 * Handles validation for user login requests.
 * Ensures email and password are provided and properly formatted.
 */
class LoginRequest extends Request
{
    /**
     * Get the validation rules for login request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
    }
}
