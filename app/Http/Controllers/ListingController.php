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
                'success' => false,
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
                'success' => false,
                'message' => 'Failed to create directory listing'
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
                'success' => false,
                'message' => 'Failed to create directory listing'
            ], 500);
        }

        return rest_ensure_response([
            'success' => true,
            'message' => 'Directory listing created successfully',
            'listing_data' => $res,
        ]);
    }

    public static function updateDirectoryListing(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
    
        if (!$id) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Directory listing ID is required'
            ]);
        }
    
        $validatedRequest = new StoreListingRequest($request);
        if ($validatedRequest->fails()) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validatedRequest->errors()
            ]);
        }
    
        $res = ListingResource::update($validatedRequest->validated(), $id);
    
        if (!$res) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Failed to update directory listing'
            ]);
        }
    
        return rest_ensure_response([
            'success' => true,
            'message' => 'Directory Listing updated successfully',
            'listing_data' => $res
        ]);
    }
    

    public static function deleteList(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Directory listing ID is required'
            ], 400);
        }
        $res = ListingResource::delete($id);
        if (!$res) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Failed to delete directory listing'
            ], 500);
        }
        return rest_ensure_response([
            'success' => true,
            'message' => 'Directory listing deleted successfully'
        ]);
    }

    public static function getAllListingByIdById(WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (!$id) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Directory listing ID is required'
            ], 400);
        }

        $res = ListingResource::get($id);

        if (!$res) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Failed to get directory listing'
            ], 500);
        }
        return wp_send_json_success(array(
            'success' => true,
            'message' => 'Directory listing retrieved successfully',
            'listing_data' => $res
        ));
    }

    public static function getAllListings()
    {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : null;
        $category_ids = isset($_GET['categories']) && !empty($_GET['categories']) ? explode(',', sanitize_text_field($_GET['categories'])) : null;
        $tag_ids = isset($_GET['tags']) && !empty($_GET['tags']) ? explode(',', sanitize_text_field($_GET['tags'])) : null;
        $radius = isset($_GET['radius']) ? intval($_GET['radius']) : null;
        $lat = isset($_GET['latitude']) ? floatval($_GET['latitude']) : null;
        $lng = isset($_GET['longitude']) ? floatval($_GET['longitude']) : null;

        $res = ListingResource::getAll($limit, $page, $search, $category_ids, $tag_ids, $lat, $lng, $radius);
        $data = $res['data'];

        if (!$res) {
            return rest_ensure_response([
                'success' => false,
                'message' => 'Failed to get directory listings'
            ], 500);
        }

        return rest_ensure_response([
            'success' => true,
            'message' => 'Directory listing retrieved successfully',
            'listings_data' => $data,
            'total' => $res['total'],
            'per_page' => $res['per_page'],
            'current_page' => $res['current_page'],
            'last_page' => $res['last_page'],


        ]);
    }

    public static function submitFrom(WP_REST_Request $request)
    {
        $data = $request->get_json_params();
    
        $listing_email    = sanitize_text_field($data['listing_email'] ?? '');
        $name    = sanitize_text_field($data['name'] ?? '');
        $email   = sanitize_email($data['email'] ?? '');
        $message = sanitize_textarea_field($data['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json([
                'success' => false,
                'message' => 'Please fill in all required fields.',
            ], 400); 
        }
    
        if (!is_email($email)) {
            wp_send_json([
                'success' => false,
                'message' => 'Invalid email address.',
            ], 400);
        }
  
        $to = $listing_email;
        $subject = "New Contact Form Message from $name";
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: Your Site <no-reply@yourdomain.com>',
            "Reply-To: $name <$listing_email>"
        ];
        $body = "<strong>Name:</strong> $name<br>
        <strong>Email:</strong> $email<br>
        <strong>Message:</strong><br>" . nl2br($message);

         $sent = wp_mail($to, $subject, $body, $headers);
        //$sent = wp_mail('chadnitalukder2@gmail.com', 'Test Subject', 'Test Body');
     
        if (!$sent) {
            error_log('wp_mail failed sending contact form email.');
        }
        wp_send_json([
            'success' => true,
            'success' => $sent,
            'message' => $sent ? 'Message sent successfully!' : 'Failed to send message.',
        ]);
    }
}
