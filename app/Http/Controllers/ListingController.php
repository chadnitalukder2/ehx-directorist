<?php

namespace EhxDirectorist\Http\Controllers;

use WP_REST_Request;
use EhxDirectorist\Http\Requests\StoreCategoryRequest;
use EhxDirectorist\Http\Requests\StoreListingRequest;
use EhxDirectorist\Resources\CategoryResource;
use EhxDirectorist\Resources\ListingResource;

class ListingController
{
    public static function storeListing(StoreListingRequest  $validatedRequest)
    {
        var_dump($validatedRequest->validated());

        if ($validatedRequest->fails()) {
            return rest_ensure_response([
                'message' => 'Validation failed',
                'errors'  => $validatedRequest->errors()
            ], 400);
        }
    
        $res = ListingResource::store($validatedRequest->validated());

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


    public static function updateCategory(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Category ID is required'
            ], 400);
        }

        $validatedRequest = new StoreCategoryRequest($request);
        if ($validatedRequest->fails()) {
            return rest_ensure_response([
                'message' => 'Validation failed',
                'errors'  => $validatedRequest->errors()
            ], 400);
        }
        $res = CategoryResource::update($validatedRequest->validated(), $id);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to update category'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Category updated successfully',
            'category_data' => $res
        ]);
    }

    public static function deleteCategory(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Category ID is required'
            ], 400);
        }
        $res = CategoryResource::delete($id);
        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to delete category'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Category deleted successfully'
        ]);
    }

    public static function getListing(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Listing ID is required'
            ], 400);
        }
 
        $res = ListingResource::get($id);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get listing'
            ], 500);
        }
        return wp_send_json_success( array(
            'message' => 'Listing retrieved successfully',
            'listing_data' => $res
        ));
    }

    public static function getAllListings()
    {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : null;

        $res = ListingResource::getAll($limit, $page, $search);
        $data = array_map(fn($cat) => $cat->toArray(), $res['data']);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get categories'
            ], 500);
        }

        return rest_ensure_response([
            'message' => 'List retrieved successfully',
            'listings_data' => $data,
            'total' => $res['total'],
            'per_page' => $res['per_page'],
            'current_page' => $res['current_page'],
            'last_page' => $res['last_page'],
        

        ]);
    }
}
