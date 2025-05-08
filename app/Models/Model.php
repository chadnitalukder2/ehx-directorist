<?php

namespace EhxDirectorist\Models;

use wpdb;

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $attributes = [];

    /** @var wpdb */
    protected $db;

    public function __construct(array $attributes = [])
    {
        global $wpdb;
        $this->db = $wpdb;

        if (!isset($this->table)) {
            throw new \Exception('Table name must be defined.');
        }

        // Prepend prefix if not already there
        if (strpos($this->table, $wpdb->prefix) !== 0) {
            $this->table = $wpdb->prefix . $this->table;
        }

        // Fill on construct
        if (!empty($attributes)) {
            $this->fill($attributes);
        }
    }

    public function fill(array $data)
    {
        $this->attributes = [];

        if (!empty($this->fillable)) {
            foreach ($this->fillable as $field) {
                if (array_key_exists($field, $data)) {
                    $this->attributes[$field] = $data[$field];
                }
            }

            // Always allow the primary key to be filled
            if (isset($data[$this->primaryKey])) {
                $this->attributes[$this->primaryKey] = $data[$this->primaryKey];
            }
        } else {
            // Allow all data if fillable is not defined
            $this->attributes = $data;
        }

        return $this;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function toArray()
    {
        return $this->attributes;
    }

    public function get()
    {
        return $this->attributes;
    }

    public function save()
    {
        if (isset($this->attributes[$this->primaryKey])) {
            $id = $this->attributes[$this->primaryKey];
            unset($this->attributes[$this->primaryKey]);

            $this->db->update($this->table, $this->attributes, [$this->primaryKey => $id]);

            $this->attributes[$this->primaryKey] = $id;
        } else {
            $this->db->insert($this->table, $this->attributes);
            $this->attributes[$this->primaryKey] = $this->db->insert_id;
        }

        return $this;
    }

    public function all()
    {
        $results = $this->db->get_results("SELECT * FROM {$this->table} ORDER BY id DESC", ARRAY_A);
        return array_map(fn($row) => (new static())->fill($row), $results);
    }
    public function paginate($perPage = 10, $page = 1, $search = '')
    {
        $offset = ($page - 1) * $perPage;
        $search_sql = '';
        $params = [];

        if (!empty($search)) {
            $search_sql = "WHERE name LIKE %s OR slug LIKE %s";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $query = "SELECT * FROM {$this->table} {$search_sql} ORDER BY id DESC LIMIT %d OFFSET %d";
        $params[] = $perPage;
        $params[] = $offset;

        $results = $this->db->get_results($this->db->prepare($query, ...$params), ARRAY_A);

        // Count total with search
        if (!empty($search)) {
            $count_query = "SELECT COUNT(*) FROM {$this->table} WHERE name LIKE %s OR slug LIKE %s";
            $total = $this->db->get_var($this->db->prepare($count_query, '%' . $search . '%', '%' . $search . '%'));
        } else {
            $total = $this->db->get_var("SELECT COUNT(*) FROM {$this->table}");
        }

        return [
            'data' => array_map(fn($row) => (new static())->fill($row), $results),
            'total' => (int)$total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }


    public function find($id)
    {
        $result = $this->db->get_row(
            $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = %d", $id),
            ARRAY_A
        );

        return $result ? (new static())->fill($result) : null;
    }

    public function where($column, $operator, $value = null)
    {
        dd($column, $operator, $value, 'hello');
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $sql = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} {$operator} %s", $value);
        $results = $this->db->get_results($sql, ARRAY_A);

        return array_map(fn($row) => (new static())->fill($row), $results);
    }

    public function whereBetween($column, array $range)
    {
        if (count($range) !== 2) {
            throw new \InvalidArgumentException("Range must contain exactly two values.");
        }

        $sql = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$column} BETWEEN %s AND %s",
            $range[0],
            $range[1]
        );

        $results = $this->db->get_results($sql, ARRAY_A);
        return array_map(fn($row) => (new static())->fill($row), $results);
    }

    public function update(array $data, $id)
    {

        $filtered = !empty($this->fillable)
            ? array_filter($data, fn($key) => in_array($key, $this->fillable), ARRAY_FILTER_USE_KEY)
            : $data;

        return $this->db->update($this->table, $filtered, [$this->primaryKey => $id]);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }

    /**
     * Create a new model in the database.
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes)
    {
        $model = new static($attributes);
        $model->save();
        return $model;
    }

    // ORM Relationship: hasOne
    public function hasOne($relatedClass, $foreignKey, $localKey = null)
    {
        $instance = new $relatedClass();
        $localKey = $localKey ?: $this->primaryKey;

        if (!isset($this->attributes[$localKey])) {
            return null;
        }

        return $instance->where($foreignKey, $this->attributes[$localKey])[0] ?? null;
    }

    // ORM Relationship: hasMany
    public function hasMany($relatedClass, $foreignKey, $localKey = null)
    {
        $instance = new $relatedClass();
        $localKey = $localKey ?: $this->primaryKey;

        if (!isset($this->attributes[$localKey])) {
            return [];
        }

        return $instance->where($foreignKey, $this->attributes[$localKey]);
    }
}
