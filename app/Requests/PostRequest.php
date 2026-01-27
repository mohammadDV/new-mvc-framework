<?php

namespace App\Requests;

use System\Request\Request;

class PostRequest extends Request
{
    public function rules() :array
    {

        if (!empty($_GET['file']['name'])) {
            return [
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:1000',
                'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|string|max:255',
            ];
        }
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'status' => 'required|string|max:255',
        ];
    }
}