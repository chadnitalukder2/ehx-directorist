<?php

namespace EhxDirectorist\Models;

class Post extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_title',
        'post_content',
        'post_status',
        'post_type',
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_modified',
        'post_modified_gmt',
        'post_parent',
        'menu_order',
        'post_mime_type',
        'comment_count',
    ];

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