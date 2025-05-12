<?php

namespace EhxDirectorist\Models;

class Listing extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ehxd_directory_listing';

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
    protected $fillable = [
        'id',
        'directory_builder_id',
        'category_id',
        'post_id',
        'post_url',
        'tag_id',
        'name',
        'phone',
        'email',
        'website_url',
        'address',
        'latitude',
        'longitude',
        'postal_code',
        'city',
        'logo',
        'image',
        'short_description',
        'description',
        'meta',
        'created_at',
        'updated_at'
    ];

    /**
     * Get the branch associated with this category
     *
     * @return Branch|null
     */
    public function branch() {
        if (!$this->branch_id) {
            return null;
        }
        
      //  return Branch::find($this->branch_id);
    }

    /**
     * Get all products belonging to this category
     *
     * @return array
     */
    public function post() {
        return $this->hasOne(Post::class, 'ID', 'post_id');
    }

    /**
     * Create a new category with the current timestamp
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