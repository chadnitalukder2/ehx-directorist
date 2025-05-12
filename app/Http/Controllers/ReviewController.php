<?php

namespace EhxDirectorist\Http\Controllers;

use WP_REST_Request;
use EhxDirectorist\Http\Requests\StoreTagRequest;
use EhxDirectorist\Resources\ReviewResource;
use EhxDirectorist\Resources\TagResource;

class ReviewController
{
    public static function getAllReviews()
    {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : null;

        $res = TagResource::getAll($limit, $page, $search);
        $data = array_map(fn($review) => $review->toArray(), $res['data']);

        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to get categories'
            ], 500);
        }

        return rest_ensure_response([
            'message' => 'Review retrieved successfully',
            'reviews_data' => $data,
            'total' => $res['total'],
            'per_page' => $res['per_page'],
            'current_page' => $res['current_page'],
            'last_page' => $res['last_page'],

        ]);
    }

    public static function deleteReview(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'message' => 'Review ID is required'
            ], 400);
        }
        $res = ReviewResource::delete($id);
        if (!$res) {
            return rest_ensure_response([
                'message' => 'Failed to delete review'
            ], 500);
        }
        return rest_ensure_response([
            'message' => 'Review data deleted successfully'
        ]);
    }

}
