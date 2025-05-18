<?php

namespace EhxDirectorist\Models;

use wpdb;

/**
 * Collection class for managing arrays of models
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate
{
    protected $items = [];
    
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
    
    /**
     * Convert all models in the collection to arrays.
     * 
     * @param array $with Relations to include
     * @return array
     */
    public function toArray(array $with = [])
    {
        return array_map(function ($model) use ($with) {
            return $model instanceof Model ? $model->toArray($with) : (array) $model;
        }, $this->items);
    }
    
    /**
     * Count the number of items in the collection.
     * 
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
    
    /**
     * Get an iterator for the items.
     * 
     * @return \ArrayIterator
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }
    
    /**
     * Determine if an item exists at an offset.
     * 
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }
    
    /**
     * Get an item at a given offset.
     * 
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }
    
    /**
     * Set the item at a given offset.
     * 
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }
    
    /**
     * Unset the item at a given offset.
     * 
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
    
    /**
     * Get all items in the collection.
     * 
     * @return array
     */
    public function all()
    {
        return $this->items;
    }
    
    /**
     * Get the first item from the collection.
     * 
     * @return mixed
     */
    public function first()
    {
        return count($this->items) > 0 ? reset($this->items) : null;
    }
    
    /**
     * Map the collection to another array.
     * 
     * @param callable $callback
     * @return Collection
     */
    public function map(callable $callback)
    {
        return new static(array_map($callback, $this->items));
    }
    
    /**
     * Filter items by the given callback.
     * 
     * @param callable|null $callback
     * @return Collection
     */
    public function filter(callable $callback = null)
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback));
        }
        
        return new static(array_filter($this->items));
    }
}

abstract class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $attributes = [];
    protected $relations = [];
    protected $query = [
        'where' => [],
        'whereIn' => [],
        'orderBy' => null,
        'limit' => null,
        'offset' => null
    ];

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

    /**
     * Add a conditional clause to the query
     * 
     * @param mixed $value Value to check against
     * @param callable $callback Callback to execute if $value is truthy
     * @param callable|null $default Callback to execute if $value is falsy
     * @return $this
     */
    public function when($value, callable $callback, callable $default = null)
    {
        if ($value) {
            return $callback($this);
        } elseif ($default) {
            return $default($this);
        }
        
        return $this;
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
        // First check if the attribute exists
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        
        // Then check if this is a loaded relationship
        if (array_key_exists($key, $this->relations)) {
            return $this->relations[$key];
        }
        
        // Finally, check if this is a relationship method
        if (method_exists($this, $key)) {
            $relation = $this->$key();
            $this->relations[$key] = $relation;
            return $relation;
        }
        
        return null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Convert the model instance to an array.
     * 
     * @param array $with Array of relationship names to include
     * @return array
     */
    public function toArray(array $with = [])
    {
        $array = $this->attributes;
        
        // Include requested relationships
        foreach ($with as $relation) {
            if (method_exists($this, $relation)) {
                $related = $this->$relation();
                
                if ($related instanceof Model) {
                    // Single model relationship
                    $array[$relation] = $related->toArray();
                } elseif (is_array($related)) {
                    // Collection of models
                    $array[$relation] = array_map(function ($item) {
                        return $item instanceof Model ? $item->toArray() : $item;
                    }, $related);
                }
            }
        }
        
        return $array;
    }

    /**
     * Get the current model's attributes.
     *
     * @return array
     */
    public function getAttributes()
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
        $this->resetQuery();
        return $this->get();
    }
    
    /**
     * Get a collection of all records.
     * 
     * @return Collection
     */
    public function getAll()
    {
        return $this->all();
    }
    
    public function paginate($perPage = 10, $page = 1, $search = '')
    {
        // Save the current query
        $currentQuery = $this->query;
        
        // Add search conditions if provided directly to paginate
        if (!empty($search)) {
            $this->where('name', 'LIKE', '%' . $search . '%')
                 ->orWhere('slug', 'LIKE', '%' . $search . '%');
        }
        
        // Add pagination limits
        $offset = ($page - 1) * $perPage;
        $this->query['limit'] = $perPage;
        $this->query['offset'] = $offset;
        
        // Get the paginated results
        $results = $this->get();
        
        // Reset query and restore conditions for count query
        $this->query = $currentQuery;
        
        // Prepare count query
        $countSql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        
        // Process where conditions for count
        if (!empty($this->query['where'])) {
            $countSql .= " WHERE ";
            $firstWhere = true;
            
            foreach ($this->query['where'] as $condition) {
                // Add the boolean operator (AND/OR) except for the first condition
                if (!$firstWhere) {
                    $countSql .= " {$condition['boolean']} ";
                } else {
                    $firstWhere = false;
                }
                
                $countSql .= "{$condition['column']} {$condition['operator']} %s";
                $params[] = $condition['value'];
            }
        }
        
        // Process whereIn conditions for count
        if (!empty($this->query['whereIn'])) {
            if (empty($this->query['where'])) {
                $countSql .= " WHERE ";
            } else {
                $countSql .= " AND ";
            }
            
            $whereInClauses = [];
            foreach ($this->query['whereIn'] as $condition) {
                if (empty($condition['values'])) {
                    continue;
                }
                
                $placeholders = implode(', ', array_fill(0, count($condition['values']), '%s'));
                $whereInClauses[] = "{$condition['column']} IN ({$placeholders})";
                $params = array_merge($params, $condition['values']);
            }
            
            $countSql .= implode(' AND ', $whereInClauses);
        }
        
        // Prepare and execute count query
        if (!empty($params)) {
            $countSql = $this->db->prepare($countSql, ...$params);
        }
        
        $total = (int) $this->db->get_var($countSql);
        
        // Reset query for next use
        $this->resetQuery();
        
        return [
            'data' => $results->all(),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }
    
    // public function filterForListing($perPage = 10, $page = 1, $search = '', $category_ids = null, $tag_ids = null)
    // {
    //     return $this
    //         ->when(!empty($search), function ($query) use ($search) {
    //             return $query->where('address', 'LIKE', '%' . $search . '%')
    //                        ->orWhere('city', 'LIKE', '%' . $search . '%')
    //                        ->orWhere('postal_code', 'LIKE', '%' . $search . '%');
    //         })
    //         ->when(!empty($category_ids), function ($query) use ($category_ids) {
    //             return $query->where('category_id', 'LIKE', '%' . $category_ids . '%');
    //         })
    //         ->when(!empty($tag_ids), function ($query) use ($tag_ids) {
    //             return $query->where('tag_id', 'LIKE', '%' . $tag_ids . '%');
    //         })
    //         ->paginate($perPage, $page);
    // }

    /**
     * Find a record by its primary key.
     *
     * @param int|string $id
     * @return static|null
     */
    public function find($id)
    {
        $this->resetQuery();
        $result = $this->db->get_row(
            $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = %s", $id),
            ARRAY_A
        );

        return $result ? (new static())->fill($result) : null;
    }

    /**
     * Find multiple records by their primary keys.
     *
     * @param array $ids
     * @return Collection
     */
    public function findMany(array $ids)
    {
        $this->resetQuery();
        return $this->whereIn($this->primaryKey, $ids)->get();
    }

    /**
     * Execute the query and get the first result.
     *
     * @return static|null
     */
    public function first()
    {
        $this->query['limit'] = 1;
        $results = $this->get();
        
        return $results->count() > 0 ? $results->first() : null;
    }

    /**
     * Reset the query constraints
     * 
     * @return $this
     */
    protected function resetQuery()
    {
        $this->query = [
            'where' => [],
            'whereIn' => [],
            'orderBy' => null,
            'limit' => null,
            'offset' => null
        ];
        
        return $this;
    }
    
    /**
     * Add a basic where clause to the query.
     * 
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return $this
     */
    public function where($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        // Convert 'like' operator to MySQL's LIKE syntax
        if (strtolower($operator) == 'like') {
            $operator = 'LIKE';
        }
        
        $this->query['where'][] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'AND'
        ];
        
        return $this;
    }

    /**
     * Add an "or where" clause to the query.
     * 
     * @param string $column
     * @param mixed $operator
     * @param mixed $value
     * @return $this
     */
    public function orWhere($column, $operator, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }
        
        // Convert 'like' operator to MySQL's LIKE syntax
        if (strtolower($operator) == 'like') {
            $operator = 'LIKE';
        }
        
        $this->query['where'][] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => 'OR'
        ];
        
        return $this;
    }

    /**
     * Add a "where in" clause to the query.
     * 
     * @param string $column The column name
     * @param array $values The values to check against
     * @return $this
     */
    public function whereIn($column, array $values)
    {
        if (!empty($values)) {
            $this->query['whereIn'][] = [
                'column' => $column,
                'values' => $values
            ];
        }
        
        return $this;
    }
    
    /**
     * Add a "where between" clause to the query.
     *
     * @param string $column
     * @param array $range Array with exactly two values
     * @return $this
     */
    public function whereBetween($column, array $range)
    {
        if (count($range) !== 2) {
            throw new \InvalidArgumentException("Range must contain exactly two values.");
        }

        $this->where($column, '>=', $range[0]);
        $this->where($column, '<=', $range[1]);
        
        return $this;
    }

    /**
     * Add an order by clause to the query.
     *
     * @param string $column
     * @param string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->query['orderBy'] = [$column, strtoupper($direction)];
        return $this;
    }

    /**
     * Set the number of rows to return.
     *
     * @param int $value
     * @return $this
     */
    public function limit($value)
    {
        $this->query['limit'] = (int) $value;
        return $this;
    }

    /**
     * Set the offset for the query.
     *
     * @param int $value
     * @return $this
     */
    public function offset($value)
    {
        $this->query['offset'] = (int) $value;
        return $this;
    }
    
    /**
     * Execute the query and get the results.
     * 
     * @return Collection
     */
    public function get()
    {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        // Process where conditions
        if (!empty($this->query['where'])) {
            $sql .= " WHERE ";
            $firstWhere = true;
            
            foreach ($this->query['where'] as $condition) {
                // Add the boolean operator (AND/OR) except for the first condition
                if (!$firstWhere) {
                    $sql .= " {$condition['boolean']} ";
                } else {
                    $firstWhere = false;
                }
                
                $sql .= "{$condition['column']} {$condition['operator']} %s";
                $params[] = $condition['value'];
            }
        }
        
        // Process whereIn conditions
        if (!empty($this->query['whereIn'])) {
            if (empty($this->query['where'])) {
                $sql .= " WHERE ";
            } else {
                $sql .= " AND ";
            }
            
            $whereInClauses = [];
            foreach ($this->query['whereIn'] as $condition) {
                if (empty($condition['values'])) {
                    continue;
                }
                
                $placeholders = implode(', ', array_fill(0, count($condition['values']), '%s'));
                $whereInClauses[] = "{$condition['column']} IN ({$placeholders})";
                $params = array_merge($params, $condition['values']);
            }
            
            $sql .= implode(' AND ', $whereInClauses);
        }
        
        // Add order by if set
        if ($this->query['orderBy']) {
            $sql .= " ORDER BY {$this->query['orderBy'][0]} {$this->query['orderBy'][1]}";
        } else {
            $sql .= " ORDER BY {$this->primaryKey} DESC";
        }
        
        // Add limit and offset if set
        if ($this->query['limit'] !== null) {
            $sql .= " LIMIT %d";
            $params[] = $this->query['limit'];
            
            if ($this->query['offset'] !== null) {
                $sql .= " OFFSET %d";
                $params[] = $this->query['offset'];
            }
        }
        
        // Prepare and execute query
        if (!empty($params)) {
            $sql = $this->db->prepare($sql, ...$params);
        }
        
        $results = $this->db->get_results($sql, ARRAY_A);
        
        // Reset query for next use
        $this->resetQuery();
        
        // Map results to model instances and return as Collection
        return new Collection(array_map(fn($row) => (new static())->fill($row), $results));
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

    /**
     * Chain relationship queries for better performance.
     *
     * @param string $relationship Relationship method name
     * @param callable $callback Callback to modify the query
     * @return $this
     */
    public function with($relationship, callable $callback = null)
    {
        if (!method_exists($this, $relationship)) {
            return $this;
        }
        
        // Get the relationship
        $related = $this->$relationship();
        
        // Apply callback if provided
        if ($callback && is_callable($callback) && $related) {
            if (is_array($related)) {
                // For hasMany relationships
                foreach ($related as $item) {
                    $callback($item);
                }
            } else {
                // For hasOne relationships
                $callback($related);
            }
        }
        
        // Store the relationship
        $this->relations[$relationship] = $related;
        
        return $this;
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