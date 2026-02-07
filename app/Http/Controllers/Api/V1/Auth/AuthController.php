<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function register()
    {
        // validate name, email, password
        // create user with hashed password
        // generate token
        // return user + token
    }

    public function login()
    {
        // validate email, password
        // attempt auth
        // return token or 401 error
    }

    public function logout()
    {
        // invalidate token
        // return success message
    }

    public function me()
    {
        // return current user
    }
}
