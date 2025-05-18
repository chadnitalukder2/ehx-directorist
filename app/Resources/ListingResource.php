<?php

namespace EhxDirectorist\Resources;

use EhxDirectorist\Models\Category;
use EhxDirectorist\Models\Listing;
use EhxDirectorist\Models\Post;
use EhxDirectorist\Models\Tag;
use EhxDirectorist\Services\Arr;

class ListingResource
{
    public static function store($data)
    {

        $data['category_id'] = json_encode($data['category_id']);
        $data['tag_id'] = json_encode($data['tag_id']);
        $data['meta'] = json_encode($data['meta']);


        return Listing::create($data);
    }

    public static function update($data, $id)
    {
        $data['category_id'] = json_encode($data['category_id']);
        $data['tag_id'] = json_encode($data['tag_id']);
        $data['meta'] = json_encode($data['meta']);

        $listingModel = new Listing();

        $listing = $listingModel->find($id);

        if ($listing) {
            return $listing->update($data, $id);
        }

        return false;
    }

    public static function delete($id)
    {
        $listingModel = new Listing();
        $listing = $listingModel->find($id);

        if ($listing) {
            $listing->delete($id);
            return true;
        }

        return false;
    }

    public static function get($id)
    {
        $listing = (new Listing())->find($id);
        if (!$listing) {
            return null;
        }
        $listing->meta = json_decode($listing->meta);
        $listing->category_id = json_decode($listing->category_id);
        $listing->tag_id = json_decode($listing->tag_id);
        return $listing->toArray();
    }

    public static function getAll($perPage, $page, $search, $category_ids, $tag_ids)
    {
        global $wpdb;
    
        // Define table names
        $listing_table = $wpdb->prefix . 'ehxd_directory_listing';
        $category_table = $wpdb->prefix . 'ehxd_categories'; // Assuming this is your category table
        $tag_table = $wpdb->prefix . 'ehxd_tag'; // Assuming this is your tag table
        
           // Calculate offset for pagination
        $offset = ($page - 1) * $perPage;
        
        // Base queries
        $sql = "SELECT * FROM {$listing_table}";
        $countSql = "SELECT COUNT(*) FROM {$listing_table}";
        
        // Add WHERE conditions
        $where_conditions = [];
        $where_args = [];
        
        // Search condition for address or postal code
        if (!empty($search)) {
            $search_placeholder = '%' . $wpdb->esc_like($search) . '%';
            $where_conditions[] = "(address LIKE %s OR postal_code LIKE %s)";
            $where_args[] = $search_placeholder;
            $where_args[] = $search_placeholder;
        }
        
        // Category filtering (based on JSON array in category_id column)
        if (!empty($category_ids)) {
            $category_conditions = [];
            foreach ($category_ids as $cat_id) {
                // JSON search conditions - looking for category ID in the JSON array
                // This uses LIKE to find the ID within the JSON array string
                $category_conditions[] = "category_id LIKE %s";
                $where_args[] = '%"' . $cat_id . '"%'; // Searches for "2" or "1" in JSON string like ["2","1"]
            }
            $where_conditions[] = "(" . implode(' OR ', $category_conditions) . ")";
        }
        
        // Tag filtering (similar to category)
        if (!empty($tag_ids)) {
            $tag_conditions = [];
            foreach ($tag_ids as $tag_id) {
                $tag_conditions[] = "tag_id LIKE %s";
                $where_args[] = '%"' . $tag_id . '"%';
            }
            $where_conditions[] = "(" . implode(' OR ', $tag_conditions) . ")";
        }
        
        // Combine WHERE conditions
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(' OR ', $where_conditions);
            $countSql .= " WHERE " . implode(' OR ', $where_conditions);
        }
        
        // Add pagination
        //$sql .= " LIMIT %d OFFSET %d";
        $sql .= " ORDER BY id DESC LIMIT %d OFFSET %d";
        
        // Add pagination arguments to our query
        $query_args = array_merge($where_args, [$perPage, $offset]);
        // var_dump($sql,$query_args);
        // exit;
        // Prepare and execute queries
        $prepared_sql = $wpdb->prepare($sql, $query_args);
        $listings = $wpdb->get_results($prepared_sql);
        
        // Get total for pagination
        $total = $wpdb->get_var($wpdb->prepare($countSql, $where_args));
        
        // Process each listing
        foreach ($listings as $listing) {
            // Decode JSON data
            $listing->category_id = is_string($listing->category_id) ? json_decode($listing->category_id, true) : $listing->category_id;
            $listing->meta = is_string($listing->meta) ? json_decode($listing->meta, true) : $listing->meta;
            $listing->tag_id = is_string($listing->tag_id) ? json_decode($listing->tag_id, true) : $listing->tag_id;
            
            // Get associated tags
            if (!empty($listing->tag_id) && is_array($listing->tag_id)) {
                $placeholders = implode(',', array_fill(0, count($listing->tag_id), '%d'));
                $tags_sql = "SELECT * FROM {$tag_table} WHERE id IN ($placeholders)";
                $listing->tags = $wpdb->get_results(
                    $wpdb->prepare($tags_sql, $listing->tag_id),
                    ARRAY_A
                );
            } else {
                $listing->tags = [];
            }
            
            // Get associated categories
            if (!empty($listing->category_id) && is_array($listing->category_id)) {
                $placeholders = implode(',', array_fill(0, count($listing->category_id), '%d'));
                $categories_sql = "SELECT * FROM {$category_table} WHERE id IN ($placeholders)";
                $listing->categories = $wpdb->get_results(
                    $wpdb->prepare($categories_sql, $listing->category_id),
                    ARRAY_A
                );
            } else {
                $listing->categories = [];
            }
        }
        
        // Return in a format similar to Laravel's pagination
        return [
            'data' => $listings,
            'total' => (int)$total,
            'current_page' => (int)$page,
            'per_page' => (int)$perPage, 
            'last_page' => ceil((int)$total / (int)$perPage)
        ];
    }

    public static function getPost($id)
    {
        $post = (new Post())->find($id);
        if (!$post) {
            return null;
        }
    
        $listing = (new Listing())->where('post_id', $id)->first();
        if (!$listing) {
            return null;
        }
  
        // Decode JSON fields safely
        $listing->meta = is_string($listing->meta) ? json_decode($listing->meta, true) : $listing->meta;
        $listing->category_id = is_string($listing->category_id) ? json_decode($listing->category_id, true) : $listing->category_id;
        $listing->tag_id = is_string($listing->tag_id) ? json_decode($listing->tag_id, true) : $listing->tag_id;
        $listing->tags         = (new Tag())->whereIn('id', $listing->tag_id)->get()->toArray();
        $listing->categories   = (new Category())->whereIn('id', $listing->category_id)->get()->toArray();
    
        return $listing->toArray();
    }
}
