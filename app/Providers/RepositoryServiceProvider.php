<?php

namespace App\Providers;

use App\Repositories\Contracts\IPostRepository;
use ReflectionClass;
use ReflectionParameter;
use App\Repositories\Contracts\IUserRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserRepository;

/**
 * Repository Service Provider
 * 
 */
class RepositoryServiceProvider extends Provider
{
    /**
     * Service Provider singleton instance
     *
     * @var RepositoryServiceProvider|null
     */
    private static ?RepositoryServiceProvider $instance = null;

    /**
     * Bindings array - maps interfaces/abstract classes to concrete implementations
     *
     * @var array
     */
    private array $bindings = [];

    /**
     * Singleton instances cache - stores resolved singleton instances
     *
     * @var array
     */
    private array $instances = [];

    /**
     * Public constructor to allow provider system instantiation
     */
    public function __construct()
    {
        $this->registerDefaultBindings();
    }

    /**
     * Boot the service provider
     *
     * @return void
     */
    public function boot(): void
    {
        // Always set this instance (created by provider system) as the singleton
        self::$instance = $this;
    }

    /**
     * Get the singleton instance of the service container
     * 
     * 
     * @return RepositoryServiceProvider
     * @throws \RuntimeException
     */
    public static function getInstance(): RepositoryServiceProvider
    {
        // The instance should always be set by the provider system via boot
        if (self::$instance === null) {
            throw new \RuntimeException(
                'RepositoryServiceProvider has not been booted. '
            );
        }

        return self::$instance;
    }

    /**
     * Register default bindings
     *
     * @return void
     */
    private function registerDefaultBindings(): void
    {
        // Bind IUserRepository to UserRepository
        $this->bind(
            IUserRepository::class,
            UserRepository::class
        );
        $this->bind(
            IPostRepository::class,
            PostRepository::class
        );
    }

    /**
     * Bind an interface to a concrete implementation
     *
     * @param string $abstract
     * @param string|callable $concrete
     * @param bool $singleton
     * @return void
     */
    public function bind(string $abstract, $concrete, bool $singleton = false): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton,
        ];
    }

    /**
     * Bind a singleton instance
     *
     * @param string $abstract
     * @param string|callable $concrete
     * @return void
     */
    public function singleton(string $abstract, $concrete): void
    {
        $this->bind($abstract, $concrete, true);
    }

    /**
     * Resolve a class from the container
     *
     * @param string $abstract
     * @return object
     */
    public function make(string $abstract): object
    {
        // Check if it's a singleton and already instantiated
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        // Check if there's a binding
        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];
            $concrete = $binding['concrete'];

            // If it's a callable, execute it
            if (is_callable($concrete)) {
                $instance = $concrete($this);
            } else {
                // Resolve the concrete class
                $instance = $this->resolve($concrete);
            }

            // Store singleton instance
            if ($binding['singleton']) {
                $this->instances[$abstract] = $instance;
            }

            return $instance;
        }

        // No binding found, try to resolve directly
        return $this->resolve($abstract);
    }

    /**
     * Resolve a class with its dependencies
     *
     * @param string $class
     * @return object
     */
    private function resolve(string $class): object
    {
        $reflection = new ReflectionClass($class);

        // Check if the class can be instantiated
        if (!$reflection->isInstantiable()) {
            throw new \Exception("Class {$class} is not instantiable");
        }

        // Get the constructor
        $constructor = $reflection->getConstructor();

        // If no constructor, instantiate directly
        if (!$constructor) {
            return new $class();
        }

        $dependencies = [];

        // Resolve each dependency
        foreach ($constructor->getParameters() ?? [] as $parameter) {
            $dependencies[] = $this->resolveDependency($parameter);
        }

        // Instantiate with dependencies
        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Resolve a single dependency
     *
     * @param ReflectionParameter $parameter
     * @return object|mixed
     */
    private function resolveDependency(ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        // If no type hint, check for default value
        if ($type === null) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new \Exception("Cannot resolve dependency for parameter {$parameter->getName()}");
        }

        // Get the type name
        $typeName = $type->getName();

        // Check if it's a built-in type
        if ($type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }
            throw new \Exception("Cannot resolve built-in type for parameter {$parameter->getName()}");
        }

        // Resolve the class
        return $this->make($typeName);
    }
}