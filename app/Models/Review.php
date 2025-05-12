<?php

namespace EhxDirectorist\Models;

class Review extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ehxd_reviews';

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
     * Get the branch associated with this category
     *
     * @return Branch|null
     */
  

    /**
     * Get all products belonging to this category
     *
     * @return array
     */
  
    /**
     * Create a new category with the current timestamp
     *
     * @param array $attributes
     * @return static
     */
    // public static function create(array $attributes) {
    //     if (!isset($attributes['created_at'])) {
    //         $attributes['created_at'] = date('Y-m-d H:i:s');
    //     }
    //     return parent::create($attributes);
    // }
}