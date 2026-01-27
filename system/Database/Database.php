<?php

declare(strict_types=1);

namespace System\Database;

use PDO;
use PDOException;
use App\Exceptions\DatabaseException;

/**
 * Database Singleton Class
 * 
 */
class Database
{
    /**
     * The single instance of the database connection.
     *
     * @var Database|null
     */
    private static ?Database $instance = null;

    /**
     * The PDO connection instance.
     *
     * @var PDO|null
     */
    private ?PDO $connection = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
        $this->connect();
    }

    /**
     * Get the singleton instance of the Database class.
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Establish database connection based on configuration.
     *
     * @return void
     * @throws PDOException
     */
    private function connect(): void
    {
        $config = require __DIR__ . '/../../config/database.php';
        $defaultConnection = $config['default'];
        $connectionConfig = $config['connections'][$defaultConnection];

        try {
            if ($connectionConfig['driver'] === 'sqlite') {
                $dsn = "sqlite:" . $connectionConfig['database'];
                $this->connection = new PDO($dsn, null, null, $connectionConfig['options'] ?? []);
            } else {
                $dsn = sprintf(
                    "%s:host=%s;port=%s;dbname=%s;charset=%s",
                    $connectionConfig['driver'],
                    $connectionConfig['host'],
                    $connectionConfig['port'],
                    $connectionConfig['database'],
                    $connectionConfig['charset']
                );

                $this->connection = new PDO(
                    $dsn,
                    $connectionConfig['username'],
                    $connectionConfig['password'],
                    $connectionConfig['options'] ?? []
                );
            }
        } catch (PDOException $e) {
            throw new DatabaseException("Database connection failed: " . $e->getMessage(), $e);
        }
    }

    /**
     * Get the PDO connection instance.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $query
     * @param array $bindings
     * @return \PDOStatement
     * @throws \App\Exceptions\DatabaseException
     */
    public function query(string $query, array $bindings = []): \PDOStatement
    {
        try {
            $statement = $this->connection->prepare($query);
            $statement->execute($bindings);
            return $statement;
        } catch (PDOException $e) {
            throw new \App\Exceptions\DatabaseException(
                "Database query failed: " . $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Execute a raw SQL query and return all results.
     *
     * @param string $query
     * @param array $bindings
     * @return array
     * @throws \App\Exceptions\DatabaseException
     */
    public function fetchAll(string $query, array $bindings = []): array
    {
        try {
            $statement = $this->query($query, $bindings);
            return $statement->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\App\Exceptions\DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \App\Exceptions\DatabaseException(
                "Failed to fetch results: " . $e->getMessage(),
                $e instanceof PDOException ? $e : null
            );
        }
    }

    /**
     * Execute a raw SQL query and return a single result.
     *
     * @param string $query
     * @param array $bindings
     * @return array|false
     * @throws \App\Exceptions\DatabaseException
     */
    public function fetchOne(string $query, array $bindings = [])
    {
        try {
            $statement = $this->query($query, $bindings);
            return $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (\App\Exceptions\DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \App\Exceptions\DatabaseException(
                "Failed to fetch result: " . $e->getMessage(),
                $e instanceof PDOException ? $e : null
            );
        }
    }

    /**
     * Begin a database transaction.
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit a database transaction.
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback a database transaction.
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Get the last inserted ID.
     *
     * @return string
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }
}