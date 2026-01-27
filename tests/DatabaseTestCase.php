<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use System\Database\Database;
use ReflectionClass;
use ReflectionProperty;

/**
 * Base Test Case for Database Integration Tests
 * 
 */
abstract class DatabaseTestCase extends TestCase
{
    /**
     * The database instance.
     *
     * @var Database
     */
    protected Database $db;

    /**
     * Set up the test environment before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset the database singleton instance for testing
        $this->resetDatabaseInstance();
        
        // Get the database instance
        $this->db = Database::getInstance();
        
        // Create tables if using SQLite (for in-memory databases)
        $this->createTestTables();
        
        // Begin a transaction for test isolation
        // All changes will be rolled back after each test
        $this->db->beginTransaction();
    }

    /**
     * Tear down the test environment after each test.
     */
    protected function tearDown(): void
    {
        // Rollback the transaction to clean up test data
        if ($this->db) {
            $this->db->rollback();
        }
        
        // Reset the database singleton instance
        $this->resetDatabaseInstance();
        
        parent::tearDown();
    }

    /**
     * Reset the Database singleton instance.
     * 
     */
    protected function resetDatabaseInstance(): void
    {
        $reflection = new ReflectionClass(Database::class);
        $property = $reflection->getProperty('instance');
        $property->setValue(null, null);
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $query
     * @param array $bindings
     * @return \PDOStatement
     */
    protected function executeQuery(string $query, array $bindings = []): \PDOStatement
    {
        return $this->db->query($query, $bindings);
    }

    /**
     * Fetch all results from a query.
     *
     * @param string $query
     * @param array $bindings
     * @return array
     */
    protected function fetchAll(string $query, array $bindings = []): array
    {
        return $this->db->fetchAll($query, $bindings);
    }

    /**
     * Fetch a single result from a query.
     *
     * @param string $query
     * @param array $bindings
     * @return array|false
     */
    protected function fetchOne(string $query, array $bindings = [])
    {
        return $this->db->fetchOne($query, $bindings);
    }

    /**
     * Truncate a table (useful for setup methods).
     * 
     * Note: This will be rolled back if used within a transaction.
     *
     * @param string $table
     * @return void
     */
    protected function truncateTable(string $table): void
    {
        $this->executeQuery("TRUNCATE TABLE {$table}");
    }

    /**
     * Get the count of records in a table.
     *
     * @param string $table
     * @return int
     */
    protected function getTableCount(string $table): int
    {
        $result = $this->fetchOne("SELECT COUNT(*) as count FROM {$table}");
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Assert that a record exists in the database.
     *
     * @param string $table
     * @param array $conditions
     * @param string $message
     * @return void
     */
    protected function assertDatabaseHas(string $table, array $conditions, string $message = ''): void
    {
        $whereClause = [];
        $bindings = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "{$column} = :{$column}";
            $bindings[$column] = $value;
        }
        
        $whereClause = implode(' AND ', $whereClause);
        $query = "SELECT COUNT(*) as count FROM {$table} WHERE {$whereClause}";
        
        $result = $this->fetchOne($query, $bindings);
        $count = (int) ($result['count'] ?? 0);
        
        $this->assertGreaterThan(0, $count, $message ?: "Failed asserting that record exists in {$table}");
    }

    /**
     * Assert that a record does not exist in the database.
     *
     * @param string $table
     * @param array $conditions
     * @param string $message
     * @return void
     */
    protected function assertDatabaseMissing(string $table, array $conditions, string $message = ''): void
    {
        $whereClause = [];
        $bindings = [];
        
        foreach ($conditions as $column => $value) {
            $whereClause[] = "{$column} = :{$column}";
            $bindings[$column] = $value;
        }
        
        $whereClause = implode(' AND ', $whereClause);
        $query = "SELECT COUNT(*) as count FROM {$table} WHERE {$whereClause}";
        
        $result = $this->fetchOne($query, $bindings);
        $count = (int) ($result['count'] ?? 0);
        
        $this->assertEquals(0, $count, $message ?: "Failed asserting that record does not exist in {$table}");
    }

    /**
     * Assert the count of records in a table.
     *
     * @param string $table
     * @param int $expectedCount
     * @param string $message
     * @return void
     */
    protected function assertDatabaseCount(string $table, int $expectedCount, string $message = ''): void
    {
        $actualCount = $this->getTableCount($table);
        $this->assertEquals(
            $expectedCount,
            $actualCount,
            $message ?: "Failed asserting that table {$table} has {$expectedCount} records. Found {$actualCount}."
        );
    }

    /**
     * Create test database tables.
     * 
     * This method creates the necessary tables for testing.
     * It's safe to call multiple times (tables are created only if they don't exist).
     *
     * @return void
     */
    protected function createTestTables(): void
    {
        try {
            $config = require __DIR__ . '/../config/database.php';
            $defaultConnection = $config['default'];
            $connectionConfig = $config['connections'][$defaultConnection];
            $isSqlite = ($connectionConfig['driver'] ?? '') === 'sqlite';

            if ($isSqlite) {
                // SQLite syntax
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        created_at DATETIME,
                        updated_at DATETIME
                    )
                ");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS posts (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title VARCHAR(255) NOT NULL,
                        content TEXT,
                        user_id INTEGER NOT NULL,
                        image VARCHAR(255),
                        status VARCHAR(50),
                        created_at DATETIME,
                        updated_at DATETIME,
                        FOREIGN KEY (user_id) REFERENCES users(id)
                    )
                ");
            } else {
                // MySQL syntax
                $this->db->query("
                    CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(255) NOT NULL,
                        email VARCHAR(255) NOT NULL UNIQUE,
                        password VARCHAR(255) NOT NULL,
                        created_at DATETIME,
                        updated_at DATETIME
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");

                $this->db->query("
                    CREATE TABLE IF NOT EXISTS posts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        title VARCHAR(255) NOT NULL,
                        content TEXT,
                        user_id INT NOT NULL,
                        image VARCHAR(255),
                        status VARCHAR(50),
                        created_at DATETIME,
                        updated_at DATETIME,
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }
        } catch (\Exception $e) {
            // If tables already exist, this is fine
            // The error will be caught and ignored
        }
    }
}