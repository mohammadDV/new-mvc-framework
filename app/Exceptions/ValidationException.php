<?php

namespace App\Exceptions;

use Exception;

/**
 * Validation Exception
 * 
 * Thrown when validation fails.
 */
class ValidationException extends Exception
{
    /**
     * Validation errors.
     *
     * @var array
     */
    protected array $errors = [];

    /**
     * ValidationException constructor.
     *
     * @param array $errors
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(array $errors = [], string $message = "Validation failed", int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return 422;
    }
}
