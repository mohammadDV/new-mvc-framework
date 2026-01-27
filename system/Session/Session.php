<?php

declare(strict_types=1);

namespace System\Session;

class Session
{
    /**
     * Sets a value in the session for a given key.
     * 
     * @param string $name The session key.
     * @param mixed $value The value to store in the session.
     * 
     * @return void
     */
    public function setMethod(string $name, $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Retrieves a value from the session by the specified key.
     * 
     * @param string $name The session key.
     * @return mixed
     */
    public function getMethod(string $name)
    {
        return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
    }

    /**
     * Removes a value from the session by the specified key.
     * 
     * @param string $name The session key to remove.
     * @return void
     */
    public function removeMethod(string $name): void
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * Handles dynamic method calls when a method is not found.
     * 
     * @param string $name The name of the called method.
     * @param array $arguments The arguments passed to the method.
     * @return mixed
     */
    public function __call(string $name, array $arguments) :mixed
    {
        return $this->methodCaller($name, $arguments);
    }

    /**
     * Handles static dynamic method calls.
     * 
     * @param string $name The name of the called static method.
     * @param array $arguments The arguments passed to the method.
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments) :mixed
    {
        $instance = new self();
        return call_user_func_array([$instance, $name], $arguments);
    }

    /**
     * Calls the corresponding method with the 'Method' suffix based on the dynamic method name.
     * 
     * @param string $method The base name of the method.
     * @param array $arguments The arguments passed to the method.
     * 
     * @return mixed
     */
    private function methodCaller(string $method, array $arguments) :mixed
    {
        $suffix = 'Method';
        $methodName = $method . $suffix;
        return call_user_func_array([$this, $methodName], $arguments);
    }
}