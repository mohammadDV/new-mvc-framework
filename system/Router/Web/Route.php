<?php

namespace System\Router\Web;

use System\Router\RouteCollection;


class Route {
    
    // Constants for HTTP request types
    const GET = 'get';
    const POST = 'post';
    const PUT = 'put';
    const DELETE = 'delete';

    /**
     * Static RouteCollection instance
     * @var RouteCollection|null
     */
    private static $routeCollection = null;

    /**
     * Set the RouteCollection instance
     *
     * @param RouteCollection $routeCollection
     * @return void
     */
    public static function setRouteCollection(RouteCollection $routeCollection): void
    {
        self::$routeCollection = $routeCollection;
    }

    /**
     * Get the RouteCollection instance
     *
     * @return RouteCollection
     * @throws \RuntimeException if RouteCollection is not set
     */
    private static function getRouteCollection(): RouteCollection
    {
        if (self::$routeCollection === null) {
            throw new \RuntimeException('RouteCollection has not been initialized. Please call Route::setRouteCollection() first.');
        }
        return self::$routeCollection;
    }

    /**
     * Registers a route based on the HTTP request method.
     *
     * @param string $method - The HTTP method (e.g., 'get', 'post', 'put', 'delete').
     * @param string $url - The URL path that this route responds to.
     * @param string $executeMethod
     * @param string|null $name - (Optional) A name for the route, defaults to the URL if not provided.
     * @param array $middleware - (Optional) Array of middleware class names.
     * 
     * @return void
     */
    private static function addRoute(string $method, string $url, string $executeMethod, ?string $name = null, array $middleware = []) {
        // Split the executeMethod into class and method parts
        $executeMethodParts = explode('@', $executeMethod);
        $controller = $executeMethodParts[0];
        $action = $executeMethodParts[1];

        // Get RouteCollection instance and add the route
        $routeCollection = self::getRouteCollection();
        $routeCollection->add($method)
            ->controller($controller)
            ->action($action)
            ->url($url)
            ->name($name)
            ->middleware($middleware)
            ->register();
    }

    /**
     * Registers a GET route.
     *
     * @param string $url - The URL path this route responds to.
     * @param string $executeMethod.
     * @param string|null $name - (Optional) A name for the route.
     * @param array $middleware - (Optional) Array of middleware class names.
     * 
     * @return void
     */
    public static function get(string $url, string $executeMethod, ?string $name = null, array $middleware = []) {
        self::addRoute(self::GET, $url, $executeMethod, $name, $middleware);
    }

    /**
     * Registers a POST route.
     *
     * @param string $url - The URL path this route responds to.
     * @param string $executeMethod.
     * @param string|null $name - (Optional) A name for the route.
     * @param array $middleware - (Optional) Array of middleware class names.
     * 
     * @return void
     */
    public static function post(string $url, string $executeMethod, ?string $name = null, array $middleware = []) {
        self::addRoute(self::POST, $url, $executeMethod, $name, $middleware);
    }

    /**
     * Registers a PUT route.
     *
     * @param string $url - The URL path this route responds to.
     * @param string $executeMethod.
     * @param string|null $name - (Optional) A name for the route.
     * @param array $middleware - (Optional) Array of middleware class names.
     * 
     * @return void
     */
    public static function put(string $url, string $executeMethod, ?string $name = null, array $middleware = []) {
        self::addRoute(self::PUT, $url, $executeMethod, $name, $middleware);
    }

    /**
     * Registers a DELETE route.
     *
     * @param string $url - The URL path this route responds to.
     * @param string $executeMethod - The controller and method to execute, in the form 'Class@method'.
     * @param string|null $name - (Optional) A name for the route.
     * @param array $middleware - (Optional) Array of middleware class names.
     * 
     * @return void
     */
    public static function delete(string $url, string $executeMethod, ?string $name = null, array $middleware = []) {
        self::addRoute(self::DELETE, $url, $executeMethod, $name, $middleware);
    }
}