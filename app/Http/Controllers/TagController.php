<?php
namespace EhxDirectorist\Http\Controllers;

use WP_REST_Request;
use EhxDirectorist\Http\Requests\StoreCategoryRequest;
use EhxDirectorist\Resources\CategoryResource;
use EhxDirectorist\Resources\TagResource;

class TagController {
    public static function storeCategory(StoreCategoryRequest $request) {
        $res = TagResource::store($request->validated());
        if(!$res) {
            return rest_ensure_response([
                'message' => 'Failed to create category'
            ], 500);
        }
       
        return rest_ensure_response([
            'message' => 'Category created successfully',
            'category_data' => $res
        ]);
    }

    public static function getAllTags() {
        $res = TagResource::getAll();

        if(!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get categories'
            ], 500);
        }

        return rest_ensure_response([
            'message' => 'Categories retrieved successfully',
            'categories_data' => $res,
          
        ]);
    }
}
