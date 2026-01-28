<?php

declare(strict_types=1);

namespace System\Auth;

use System\Session\Session;

class Auth
{
    /**
     * The default redirect path.
     * @var string
     */
    private $redirectTo = "/";

    /**
     * Retrieves the authenticated user from the session.
     * @return mixed
     */
    private function userMethod() :mixed
    {
        if (!Session::get('user')) {
            return redirect($this->redirectTo);
        }
        return Session::get('user');
    }

    /**
     * Checks if the user is authenticated and returns a boolean value.
     *
     * @return bool
     */
    private function checkMethod() :bool
    {
        if (!Session::get('user')) {
            redirect($this->redirectTo);
        }
        return true;
    }

    /**
     * Checks if the user is logged in.
     * @return bool
     */
    private function checkLoginMethod() :bool
    {
        if (!Session::get('user')) {
            return false;
        }
        return true;
    }

    /**
     * Logs the user out by removing the user data from the session.
     * 
     * @return void
     */
    private function logoutMethod() :void
    {
        Session::remove('user');
    }

    /**
     * Handles dynamic method calls when an undefined method is called on this instance.
     * @param string $name The name of the called method.
     * @param array $arguments The arguments passed to the method.
     * @return mixed
     */
    public function __call(string $name, array $arguments) :mixed
    {
        return $this->methodCaller($name, $arguments);
    }

    /**
     * Handles static dynamic method calls for the Auth class.
     * 
     * @param string $name The name of the called static method.
     * @param array $arguments The arguments passed to the method.
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments) :mixed
    {
        $instance = new self();
        return $instance->methodCaller($name, $arguments);
    }

    /**
     * Calls the corresponding method with the 'Method' suffix based on the dynamic method name.
     * @param string $method The base name of the method.
     * @param array $arguments The arguments passed to the method.
     * @return mixed
     */
    private function methodCaller(string $method, array $arguments) :mixed
    {
        $suffix = 'Method';
        $methodName = $method . $suffix;
        return call_user_func_array([$this, $methodName], $arguments);
    }
}