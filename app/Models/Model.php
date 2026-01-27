<?php

namespace App\Models;

use System\Database\Database;
use App\Exceptions\DatabaseException;

/**
 * Base Model Class
 * 
 * Provides common CRUD operations for all models.
 * All models should extend this class.
 */
abstract class Model
{
    /**
     * The database connection instance.
     *
     * @var \System\Database\Database
     */
    protected Database $db;

    /**
     * The name of the database table associated with the model.
     *
     * @var string
     */
    protected string $table;

    /**
     * The primary key for the model's table.
     *
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = [];

    /**
     * The attributes that should be hidden from serialization.
     *
     * @var array
     */
    protected array $hidden = [];

    /**
     * Model constructor.
     * Initializes the database connection.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all records from the table.
     *
     * @return array
     */
    public function all(): array
    {
        $query = "SELECT * FROM {$this->table}";
        return $this->db->fetchAll($query);
    }

    /**
     * Find a record by its primary key.
     *
     * @param int|string $id
     * @return array|false
     * @throws DatabaseException
     */
    public function find($id)
    {
        try {
            $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
            return $this->db->fetchOne($query, ['id' => $id]);
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException("Failed to find record: " . $e->getMessage());
        }
    }

    /**
     * Find a record by a specific column and value.
     *
     * @param string $column
     * @param mixed $value
     * @return array|false
     */
    public function findBy(string $column, $value)
    {
        $query = "SELECT * FROM {$this->table} WHERE {$column} = :value LIMIT 1";
        return $this->db->fetchOne($query, ['value' => $value]);
    }

    /**
     * Find all records matching a specific column and value.
     *
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public function findAllBy(string $column, $value): array
    {
        $query = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        return $this->db->fetchAll($query, ['value' => $value]);
    }

    /**
     * Create a new record in the database.
     *
     * @param array $data
     * @return int|string The ID of the newly created record
     * @throws DatabaseException
     */
    public function create(array $data)
    {
        try {
            // Filter data to only include fillable fields
            $data = $this->filterFillable($data);

            if (empty($data)) {
                throw new DatabaseException('No valid data provided for insertion');
            }

            $arrayKeys = array_keys($data);
            $columns = implode(', ', $arrayKeys);
            $placeholders = ':' . implode(', :', $arrayKeys);

            $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
            $this->db->query($query, $data);

            return $this->db->lastInsertId();
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException("Failed to create record: " . $e->getMessage());
        }
    }

    /**
     * Update a record in the database.
     *
     * @param int|string $id
     * @param array $data
     * @return bool
     * @throws DatabaseException
     */
    public function update($id, array $data): bool
    {
        try {
            // Filter data to only include fillable fields
            $data = $this->filterFillable($data);

            if (empty($data)) {
                throw new DatabaseException('No valid data provided for update');
            }

            $setClause = [];
            foreach (array_keys($data) as $column) {
                $setClause[] = "{$column} = :{$column}";
            }
            $setClause = implode(', ', $setClause);

            $data['id'] = $id;
            $query = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = :id";
            $statement = $this->db->query($query, $data);

            return $statement->rowCount() > 0;
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException("Failed to update record: " . $e->getMessage());
        }
    }

    /**
     * Delete a record from the database.
     *
     * @param int|string $id
     * @return bool
     * @throws DatabaseException
     */
    public function delete($id): bool
    {
        try {
            $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
            $statement = $this->db->query($query, ['id' => $id]);

            return $statement->rowCount() > 0;
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException("Failed to delete record: " . $e->getMessage());
        }
    }

    /**
     * Filter data to only include fillable fields.
     *
     * @param array $data
     * @return array
     */
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Hide specified attributes from the model.
     *
     * @param array $data
     * @return array
     */
    protected function hideAttributes(array $data): array
    {
        foreach ($this->hidden as $attribute) {
            unset($data[$attribute]);
        }

        return $data;
    }

    /**
     * Execute a custom query.
     *
     * @param string $query
     * @param array $bindings
     * @return array
     */
    public function query(string $query, array $bindings = []): array
    {
        return $this->db->fetchAll($query, $bindings);
    }

    /**
     * Execute a custom query and return a single result.
     *
     * @param string $query
     * @param array $bindings
     * @return array|false
     */
    public function queryOne(string $query, array $bindings = [])
    {
        return $this->db->fetchOne($query, $bindings);
    }

    /**
     * Get the table name for the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Get the total count of records in the table.
     *
     * @return int
     */
    public function count(): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $result = $this->db->fetchOne($query);
        return (int) ($result['count'] ?? 0);
    }

    /**
     * Get paginated records from the table.
     *
     * @param int $perPage Number of items per page
     * @param int $page Current page number
     * @return array Array with 'items' and 'total' keys
     */
    public function paginate(int $perPage = 15, int $page = 1): array
    {
        // Ensure valid page number
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        // Get total count
        $total = $this->count();

        // Get paginated items (using integers directly since PDO doesn't bind LIMIT/OFFSET well)
        $perPage = (int) $perPage;
        $offset = (int) $offset;
        $query = "SELECT * FROM {$this->table} LIMIT {$perPage} OFFSET {$offset}";
        $items = $this->db->fetchAll($query);

        return [
            'items' => $items,
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page
        ];
    }
}