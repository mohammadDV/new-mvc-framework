<?php

namespace System\Router;

use ReflectionMethod;
use App\Providers\RepositoryServiceProvider;
use App\Exceptions\NotFoundException;
use App\Middleware\IMiddleware;

class Routing {
    /**
     * The current route
     * @var array
     */
    private array $currentRoute;
    
    /**
     * The method field
     * @var string
     */
    private string $methodField;
    
    /**
     * The values
     * @var array
     */
    private array $values = [];
    
    /**
     * The service provider
     * @var RepositoryServiceProvider
     */
    private RepositoryServiceProvider $serviceProvider;

    /**
     * Constructor to initialize the current route, method field, and routes.
     *
     * @param RouteCollection $routeCollection The route collection instance
     */
    public function __construct(private RouteCollection $routeCollection)
    {
        // Split the current route from the constant CURRENT_ROUTE
        $this->currentRoute = explode('/', CURRENT_ROUTE);
        
        // Determine the HTTP method used in the request (GET, POST, PUT, DELETE, etc.)
        $this->methodField = $this->methodField();
        
        // Initialize the service provider for dependency injection
        $this->serviceProvider = RepositoryServiceProvider::getInstance();
    }

    /**
     * Main function to run the routing process. 
     */
    public function run() {
        // Check if the request is for a static file (image, CSS, JS, etc.)
        if ($this->methodField === 'get') {
            $requestPath = '/' . CURRENT_ROUTE;
            // Convert URL path to file system path
            $fileSystemPath = str_replace('/', DIRECTORY_SEPARATOR, ltrim($requestPath, '/'));
            $publicFilePath = BASE_DIR . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $fileSystemPath;
            
            // Check if the file exists and is actually a file (not a directory)
            if (file_exists($publicFilePath) && is_file($publicFilePath)) {
                $this->serveStaticFile($publicFilePath);
                return;
            }
        }
        
        // Find a matching route
        $match = $this->match();
        if(empty($match)) { 
            $this->error404();
        }

        // Execute middleware before controller
        if (isset($match['middleware']) && !empty($match['middleware'])) {
            foreach ($match['middleware'] as $middlewareClass) {
                $middleware = new $middlewareClass();
                
                if (!$middleware instanceof IMiddleware) {
                    throw new \RuntimeException("Middleware {$middlewareClass} must implement IMiddleware interface");
                }
                
                $result = $middleware->handle();
                
                // If middleware returns false or redirects, stop execution
                if ($result !== true) {
                    return;
                }
            }
        }

        // Construct the full path to the controller file
        $controllerPath  = str_replace("\\", DIRECTORY_SEPARATOR, $match['controller']);
        $path = BASE_DIR . DIRECTORY_SEPARATOR . 'APP' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $controllerPath . '.php';
        
        if(!file_exists($path)) { 
            $this->error404();
        }

        // Dynamically create an instance of the controller class using dependency injection
        $controller  = "\App\Controllers\\" . $match['controller'];
        $object = $this->serviceProvider->make($controller);

        // Check if the method exists in the controller
        if(method_exists($object, $match['action'])) {
            // Use Reflection to check the number of parameters required by the method
            $reflection = new ReflectionMethod($controller, $match['action']);
            $paramCount = $reflection->getNumberOfParameters();

            // Ensure the number of parameters matches the values passed from the URL
            if($paramCount <= count($this->values)) {
                try {
                    call_user_func_array([$object, $match['action']], $this->values);
                } catch (\Throwable $e) {
                    // Let the global exception handler deal with it
                    throw $e;
                }
            } else {
                $this->error404();
            }
        } else {
            $this->error404();
        }
    }

    /**
     * Match the current route with the reserved routes.
     * 
     * @return array
     */
    public function match() :array {
        $reservedRoutes = $this->routeCollection->getRoutes($this->methodField);

        foreach($reservedRoutes ?? [] as $reservedRoute) {
            if($this->compare($reservedRoute['url'])) {
                return [
                    'controller' => $reservedRoute['controller'],
                    'action' => $reservedRoute['action'],
                    'middleware' => $reservedRoute['middleware'] ?? [],
                ];
            } else {
                $this->values = [];
            }
        }

        return [];
    }

    /**
     * Compare the reserved route with the current route.
     * 
     * @param string $reservedRouteUrl
     * @return bool
     */
    public function compare(string $reservedRouteUrl) :bool
    {
        // If the reserved route is empty, match the root route
        if (trim($reservedRouteUrl, "/") === "") {
            return trim($this->currentRoute[0], "/") === "" ? true : false;
        }

        // Split the reserved route URL into segments and compare with current route
        $reservedRouteUrlArray = explode('/', $reservedRouteUrl);
        if (count($reservedRouteUrlArray) != count($this->currentRoute)) {
            return false;
        }

        // Compare each segment of the route
        foreach($this->currentRoute as $key => $currentRouteElement) {
            $reservedRouteUrlElement = $reservedRouteUrlArray[$key];

            // If it's a dynamic parameter, store its value
            if (substr($reservedRouteUrlElement, 0, 1) === '{' && substr($reservedRouteUrlElement, -1) === '}'){
                array_push($this->values, $currentRouteElement);
            } elseif ($reservedRouteUrlElement != $currentRouteElement) {
                return false;
            }
        }

        return true;
    }

    /**
     * Handle 404 errors by displaying a custom 404 page.
     */
    public function error404() {
        throw new NotFoundException('Page not found');
    }

    /**
     * Serve a static file from the public directory.
     * 
     * @param string $filePath The full path to the file
     */
    private function serveStaticFile(string $filePath): void
    {
        // Get the MIME type based on file extension
        $mimeType = mime_content_type($filePath);
        if (!$mimeType) {
            // Fallback MIME types for common file extensions
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'pdf' => 'application/pdf',
            ];
            $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
        }

        // Set appropriate headers
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=31536000'); 

        // Output the file
        readfile($filePath);
        exit;
    }

    /**
     * Determine the HTTP method used (GET, POST, PUT, DELETE).
     * 
     * @return string The HTTP method.
     */
    public function methodField() :string
    {
        $methodField = strtolower($_SERVER['REQUEST_METHOD']);

        // If the method is POST and there's a _method field, determine the actual method
        if($methodField === 'post') {
            if(isset($_POST['_method'])) {
                if($_POST['_method'] === 'put') { 
                    $methodField = 'put'; 
                }
                if($_POST['_method'] === 'delete') { 
                    $methodField = 'delete'; 
                }
            }
        }

        return $methodField;
    }
}