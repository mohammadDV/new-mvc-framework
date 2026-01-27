<?php
// declare(strict_types=1);

namespace System\Application;

use Dotenv\Dotenv;
use \System\Router\Web\Route;
use \System\Router\RouteCollection;
use \System\Router\Routing;
use App\Exceptions\ExceptionHandler;

class Application
{

    public function __construct()
    {
        $this->loadEnvironmentVariables();
        $this->loadAppConfig();
        $this->loadExceptionHandler();
        $this->loadProviders();
        $this->registerRoutes();
    }

    private function loadEnvironmentVariables(): void 
    {
        if (file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.env')) {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..');
            $dotenv->load();
        }
    }

    private function loadAppConfig(): int 
    {
        return require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
    }

    private function loadExceptionHandler(): void 
    {
        $exceptionHandler = new ExceptionHandler();
        $exceptionHandler->register();
    }
    
    private function loadProviders(): void
    {
        foreach (PROVIDERS as $provider)
        {
            $providerObject = new $provider();
            $providerObject->boot();
        }
    }

    
    private function registerRoutes(): void
    {
        $routeCollection = new RouteCollection();

        // Set RouteCollection on Route class so routes can be registered
        Route::setRouteCollection($routeCollection);
        require_once (BASE_DIR . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'web.php');

        $routing = new Routing($routeCollection);
        $routing->run();
    }
    
}