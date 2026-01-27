<?php

declare(strict_types=1);

namespace App\Requests;

use System\Request\Request;

/**
 * Register Request
 * 
 * Handles validation for user registration requests.
 * Ensures name, email, and password meet security requirements.
 */
class RegisterRequest extends Request
{
    /**
     * Get the validation rules for registration request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'confirm_password' => 'required|string|min:8',
        ];
    }
}