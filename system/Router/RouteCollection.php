<?php

namespace System\Router;

/**
 * RouteCollection class manages all registered routes
 * This replaces the global $routes variable
 */
class RouteCollection {
    private $routes = [];

    /**
     * Constructor to initialize routes array
     */
    public function __construct()
    {
        $this->routes = [
            'get' => [],
            'post' => [],
            'put' => [],
            'delete' => [],
        ];
    }

    /**
     * Add a route to the collection
     *
     * @param string $method HTTP method (get, post, put, delete)
     * @return RouteBuilder
     */
    public function add(string $method): RouteBuilder
    {
        $method = strtolower($method);

        if (!isset($this->routes[$method])) {
            throw new \InvalidArgumentException("Invalid HTTP method: {$method}");
        }

        return new RouteBuilder($this, $method);
    }
    
    /**
     * Register a route
     *
     * @param array $route Route array
     * @return void
     */
    public function register(array $route): void
    {
        $this->routes[$route['method']][] = $route;
    }

    /**
     * Get all routes for a specific HTTP method
     *
     * @param string $method HTTP method
     * @return array Array of routes for the method
     */
    public function getRoutes(string $method): array
    {
        return $this->routes[$method] ?? [];
    }

    /**
     * Get all routes
     *
     * @return array All routes organized by HTTP method
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }
}