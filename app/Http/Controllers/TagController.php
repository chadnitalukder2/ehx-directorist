<?php

namespace EhxDirectorist\Http\Controllers;

use WP_REST_Request;
use EhxDirectorist\Http\Requests\StoreTagRequest;
use EhxDirectorist\Resources\TagResource;

class TagController
{
    public static function storeTag(WP_REST_Request $request)
    {
        $validatedRequest = new StoreTagRequest($request);
        if ($validatedRequest->fails()) {
            return rest_ensure_response([
                'message' => 'Validation failed',
                'errors'  => $validatedRequest->errors()
            ], 400);
        }
        $res = TagResource::store($validatedRequest->validated());
        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to create category'
            ], 500);
        }

        return rest_ensure_response([
            'message' => 'Category created successfully',
            'category_data' => $res
        ]);
    }

    public static function updateTag(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Tag ID is required'
            ], 400);
        }

        $validatedRequest = new StoreTagRequest($request);
        if ($validatedRequest->fails()) {
            return rest_ensure_response([
                'message' => 'Validation failed',
                'errors'  => $validatedRequest->errors()
            ], 400);
        }
        $res = TagResource::update($validatedRequest->validated(), $id);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to update tag data'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Tag data updated successfully',
            'tags_data' => $res
        ]);
    }

    public static function getAllTag()
    {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : null;

        $res = TagResource::getAll($limit, $page, $search);
        $data = array_map(fn($tag) => $tag->toArray(), $res['data']);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get tag'
            ], 500);
        }

        return rest_ensure_response([
            'message' => 'Tag retrieved successfully',
            'tags_data' => $data,
            'total' => $res['total'],
            'per_page' => $res['per_page'],
            'current_page' => $res['current_page'],
            'last_page' => $res['last_page'],

        ]);
    }

    public static function deleteTag(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Tag ID is required'
            ], 400);
        }
        $res = TagResource::delete($id);
        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to delete tag'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Tag data deleted successfully'
        ]);
    }

}
