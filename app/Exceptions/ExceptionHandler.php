<?php

namespace App\Exceptions;

use Exception;
use Throwable;
use ErrorException;
use PDOException;

/**
 * Global Exception Handler
 * 
 * Handles all exceptions and errors in the application.
 * Provides logging, error pages, and proper error responses.
 */
class ExceptionHandler
{
    /**
     * Whether the application is in debug mode.
     *
     * @var bool
     */
    protected bool $debug;

    /**
     * The log file path.
     *
     * @var string
     */
    protected string $logPath;

    /**
     * ExceptionHandler constructor.
     */
    public function __construct()
    {
        $this->debug = $this->isDebugMode();
        $this->logPath = BASE_DIR . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'error.log';
        $this->ensureLogDirectory();
    }

    /**
     * Register the exception handler.
     *
     * @return void
     */
    public function register(): void
    {
        // Set error handler for PHP errors
        set_error_handler([$this, 'handleError']);
        
        // Set exception handler
        set_exception_handler([$this, 'handleException']);
        
        // Set shutdown handler for fatal errors
        register_shutdown_function([$this, 'handleShutdown']);
        
        // Set error reporting based on environment
        if ($this->debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '0');
        }
    }

    /**
     * Handle PHP errors.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @return bool
     * @throws ErrorException
     */
    public function handleError(int $level, string $message, string $file, int $line): bool
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }

        return false;
    }

    /**
     * Handle uncaught exceptions.
     *
     * @param Throwable $exception
     * @return void
     */
    public function handleException(Throwable $exception): void
    {
        // Log the exception
        $this->logException($exception);

        // Get HTTP status code
        $statusCode = $this->getStatusCode($exception);

        // Set HTTP response code
        http_response_code($statusCode);

        // Handle different exception types
        if ($exception instanceof PDOException) {
            $this->handleDatabaseException($exception);
        } elseif ($exception instanceof NotFoundException) {
            $this->renderErrorPage(404, $exception);
        } elseif ($exception instanceof ValidationException) {
            $this->handleValidationException($exception);
        } elseif ($exception instanceof UnauthorizedException) {
            $this->renderErrorPage(403, $exception);
        } else {
            $this->renderErrorPage($statusCode, $exception);
        }
    }

    /**
     * Handle shutdown (fatal errors).
     *
     * @return void
     */
    public function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );

            $this->handleException($exception);
        }
    }

    /**
     * Handle database exceptions.
     *
     * @param PDOException $exception
     * @return void
     */
    protected function handleDatabaseException(PDOException $exception): void
    {
        if ($this->debug) {
            $this->renderErrorPage(500, $exception, 'Database Error');
        } else {
            $this->renderErrorPage(500, null, 'A database error occurred. Please try again later.');
        }
    }

    /**
     * Handle validation exceptions.
     *
     * @param ValidationException $exception
     * @return void
     */
    protected function handleValidationException(ValidationException $exception): void
    {
        // Store errors in session
        $errors = $exception->getErrors();
        if (function_exists('error')) {
            foreach ($errors as $field => $message) {
                error($field, $message);
            }
        } else {
            // Fallback: store in session directly
            if (!isset($_SESSION['errorFlash'])) {
                $_SESSION['errorFlash'] = [];
            }
            foreach ($errors as $field => $message) {
                $_SESSION['errorFlash'][$field] = $message;
            }
        }

        // Redirect back with errors
        if (function_exists('redirect') && isset($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } elseif (function_exists('redirect')) {
            redirect('/');
        } else {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }
    }

    /**
     * Log exception to file.
     *
     * @param Throwable $exception
     * @return void
     */
    protected function logException(Throwable $exception): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $type = get_class($exception);

        $logEntry = sprintf(
            "[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
            $timestamp,
            $type,
            $message,
            $file,
            $line,
            $trace
        );

        // Ensure log directory exists
        $this->ensureLogDirectory();

        // Write to log file
        file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Render error page.
     *
     * @param int $statusCode
     * @param Throwable|null $exception
     * @param string|null $customMessage
     * @return void
     */
    protected function renderErrorPage(int $statusCode, ?Throwable $exception = null, ?string $customMessage = null): void
    {
        $errorPage = BASE_DIR . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'Router' . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . "{$statusCode}.php";

        if (file_exists($errorPage)) {
            // Extract exception details for view
            $errorDetails = [
                'statusCode' => $statusCode,
                'message' => $customMessage ?? ($exception ? $exception->getMessage() : 'An error occurred'),
                'exception' => $exception,
                'debug' => $this->debug,
            ];

            extract($errorDetails);
            include $errorPage;
        } else {
            // Fallback error page
            $this->renderFallbackErrorPage($statusCode, $customMessage);
        }

        exit;
    }

    /**
     * Render fallback error page when custom error page doesn't exist.
     *
     * @param int $statusCode
     * @param string|null $message
     * @return void
     */
    protected function renderFallbackErrorPage(int $statusCode, ?string $message = null): void
    {
        $statusMessages = [
            404 => 'Page Not Found',
            403 => 'Forbidden',
            500 => 'Internal Server Error',
            503 => 'Service Unavailable',
        ];

        $title = $statusMessages[$statusCode] ?? 'Error';
        $message = $message ?? $title;

        echo "<!DOCTYPE html>
                <html>  
                <head>
                    <title>{$statusCode} - {$title}</title>
                    <style>
                        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                        h1 { font-size: 72px; margin: 0; color: #333; }
                        p { font-size: 18px; color: #666; }
                    </style>
                </head>
                <body>
                    <h1>{$statusCode}</h1>
                    <p>{$message}</p>
                </body>
            </html>";
    }

    /**
     * Get HTTP status code from exception.
     *
     * @param Throwable $exception
     * @return int
     */
    protected function getStatusCode(Throwable $exception): int
    {
        if ($exception instanceof NotFoundException) {
            return 404;
        }

        if ($exception instanceof UnauthorizedException) {
            return 403;
        }

        if ($exception instanceof PDOException) {
            return 500;
        }

        // Check if exception has getStatusCode method
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return 500;
    }

    /**
     * Check if application is in debug mode.
     *
     * @return bool
     */
    protected function isDebugMode(): bool
    {
        if (file_exists(BASE_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'errors.php')) {
            $config = require BASE_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'errors.php';
            return $config['debug'] ?? true;
        }

        // Default to true for development
        return true;
    }

    /**
     * Ensure log directory exists.
     *
     * @return void
     */
    protected function ensureLogDirectory(): void
    {
        $logDir = dirname($this->logPath);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
}