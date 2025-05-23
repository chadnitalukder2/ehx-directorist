<?php

namespace EhxDirectorist\Models;

class Tag extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ehxd_tag';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

 

    /**
     * Create a new tag with the current timestamp
     *
     * @param array $attributes
     * @return static
     */
    public static function create(array $attributes) {
        if (!isset($attributes['created_at'])) {
            $attributes['created_at'] = date('Y-m-d H:i:s');
        }
        return parent::create($attributes);
    }
}