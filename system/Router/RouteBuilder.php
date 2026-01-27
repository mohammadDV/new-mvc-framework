<?php

declare(strict_types=1);

namespace System\Router;

class RouteBuilder
{
    private string $url;
    private string $controller;
    private string $action;
    private ?string $name = null;
    private array $middleware = [];

    /**
     * Constructor to initialize the collection and method
     *
     * @param RouteCollection $collection Route collection instance
     * @param string $method HTTP method (get, post, put, delete)
     * @return void
     */
    public function __construct(private RouteCollection $collection, private string $method)
    {
        //
    }

    /**
     * Set the URL for the route
     *
     * @param string $url URL for the route
     * @return self
     */
    public function url(string $url): self
    {
        $this->url = trim($url, '/ ');
        return $this;
    }

    /**
     * Set the controller for the route
     *
     * @param string $controller Controller class name
     * @return self
     */
    public function controller(string $controller): self
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * Set the action for the route
     *
     * @param string $action Action method name
     * @return self
     */
    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Set the name for the route
     *
     * @param string $name Name for the route
     * @return self
     */
    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set the middleware for the route
     *
     * @param array|string $middleware Middleware class name or array of middleware class names
     * @return self
     */
    public function middleware(array|string $middleware): self
    {
        $this->middleware = array_merge(
            $this->middleware,
            (array) $middleware
        );

        return $this;
    }

    /**
     * Register the route
     *
     * @return void
     */
    public function register(): void
    {
        if (!isset($this->url, $this->controller, $this->action)) {
            throw new \LogicException('Route is incomplete.');
        }

        $this->collection->register([
            'method' => $this->method,
            'url' => $this->url,
            'controller' => $this->controller,
            'action' => $this->action,
            'name' => $this->name ?? $this->url,
            'middleware' => $this->middleware,
        ]);
    }
}