<?php

namespace App\Requests;

use System\Request\Request;

class UserRequest extends Request
{
    public function rules() :array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'confirm_password' => 'required|string|min:8',
        ];
    }
}