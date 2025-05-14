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
        if ($validatedRequest->fails()) {
            return rest_ensure_response([
                'message' => 'Validation failed',
                'errors'  => $validatedRequest->errors()
            ], 400);
        }
        $name = $validatedRequest->validated()['name'];
        $description = $validatedRequest->validated()['description'];
        $post_id = wp_insert_post([
            'post_title'    => $name ?? 'Untitled',
            'post_content'  => $description ?? '',
            'post_status'   => 'publish',
            'post_type'     => 'post',
        ]);
      
        if (is_wp_error($post_id)) {
            return rest_ensure_response([
                'message' => 'Failed to create listing'
            ], 500);
        };

        wp_update_post([
            'ID'           => $post_id,
            'post_content' => '[ehxd_listing_details id="' . $post_id . '"]',
        ]);

        $listingData = $validatedRequest->validated();
        $listingData['post_id'] = $post_id;
        $listingData['post_url'] = get_permalink($post_id);

        $res = ListingResource::store($listingData);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to create listing'
            ], 500);
        }

        return rest_ensure_response([
            'message' => 'Listing created successfully',
            'listing_data' => $res,
        ]);
    }

    public static function updateDirectoryListing(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Listing ID is required'
            ], 400);
        }

        $validatedRequest = new StoreListingRequest($request);
        if ($validatedRequest->fails()) {
            return rest_ensure_response([
                'message' => 'Validation failed',
                'errors'  => $validatedRequest->errors()
            ], 400);
        }
        $res = ListingResource::update($validatedRequest->validated(), $id);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to update category'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Listing updated successfully',
            'listing_data' => $res
        ]);
    }

    public static function deleteList(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Listing ID is required'
            ], 400);
        }
        $res = ListingResource::delete($id);
        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to delete listing'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'listing deleted successfully'
        ]);
    }

    public static function getAllListingByIdById(WP_REST_Request $request)
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
        $category_ids = isset($_GET['categories']) ? explode(',', sanitize_text_field($_GET['categories'])) : [];
        $tag_ids = isset($_GET['tags']) ? explode(',', sanitize_text_field($_GET['tags'])) : [];
       
        $res = ListingResource::getAll($limit, $page, $search, $category_ids, $tag_ids);
        $data = array_map(fn($cat) => $cat->toArray(), $res['data']);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get listings'
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
