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
        $data['social_links'] = json_encode($data['social_links']);

        return Listing::create($data);
    }

    public static function update($data, $id)
    {
 
        $data['category_id'] = json_encode($data['category_id']);
        $data['tag_id'] = json_encode($data['tag_id']);
        $data['meta'] = json_encode($data['meta']);
        $data['social_links'] = json_encode($data['social_links']);

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
        $listing->social_links = json_decode($listing->social_links);
        $listing->category_id = json_decode($listing->category_id);
        $listing->tag_id = json_decode($listing->tag_id);
        return $listing->toArray();
    }

    public static function getAll($perPage, $page, $search, $category_ids, $tag_ids, $latitude = null, $longitude = null, $distance = null)
    {
        global $wpdb;
    
        // Define table names
        $listing_table = $wpdb->prefix . 'ehxd_directory_listing';
        $category_table = $wpdb->prefix . 'ehxd_categories';
        $tag_table = $wpdb->prefix . 'ehxd_tag';
        
        // Calculate offset for pagination
        $offset = ($page - 1) * $perPage;
    
        // Base query without distance calculation initially
        $sql = "SELECT * FROM {$listing_table}";
        $countSql = "SELECT COUNT(*) FROM {$listing_table}";
        
        // Add distance calculation if coordinates are provided
        if ($latitude && $longitude && is_numeric($latitude) && is_numeric($longitude)) {
            // Modify the SQL to include distance calculation
            $sql = "SELECT *, 
                    (6371 * acos(
                        cos(radians(%f)) * 
                        cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(%f)) + 
                        sin(radians(%f)) * 
                        sin(radians(latitude))
                    )) AS distance 
                    FROM {$listing_table}";
            
            // Prepare the lat/long values for the main query
            $sql = $wpdb->prepare($sql, $latitude, $longitude, $latitude);
        }
        
        // Add WHERE conditions
        $where_conditions = [];
        $where_args = [];
        
        // Distance condition - this is critical for filtering by radius
        if ($latitude && $longitude && $distance && 
            is_numeric($latitude) && is_numeric($longitude) && is_numeric($distance)) {
            
            // Add the distance calculation to WHERE clause
            $where_conditions[] = "(6371 * acos(
                cos(radians(%f)) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians(%f)) + 
                sin(radians(%f)) * 
                sin(radians(latitude))
            )) <= %f";
            
            $where_args[] = $latitude;
            $where_args[] = $longitude;
            $where_args[] = $latitude;
            $where_args[] = $distance;
        }
        
        // Search condition for name or postal code
        if (!empty($search)) {
            $search_placeholder = '%' . $wpdb->esc_like($search) . '%';
            $where_conditions[] = "(name LIKE %s OR postal_code LIKE %s OR address LIKE %s)";
            $where_args[] = $search_placeholder;
            $where_args[] = $search_placeholder;
            $where_args[] = $search_placeholder;
        }
        
        // Category filtering (based on JSON array in category_id column)
        if (!empty($category_ids)) {
            $category_conditions = [];
            foreach ($category_ids as $cat_id) {
                // JSON search conditions
                $category_conditions[] = "category_id LIKE %s";
                $where_args[] = '%"' . $cat_id . '"%';
            }
            $where_conditions[] = "(" . implode(' OR ', $category_conditions) . ")";
        }
        
        // Tag filtering
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
            $sql .= " WHERE " . implode(' AND ', $where_conditions);
            $countSql .= " WHERE " . implode(' AND ', $where_conditions);
        }
        
        // Add ordering by distance if applicable
        if ($latitude && $longitude && is_numeric($latitude) && is_numeric($longitude)) {
            $sql .= " ORDER BY distance ASC";
        } else {
            // Order by ID descending if no distance sorting is applied
            $sql .= " ORDER BY id DESC";
        }
        
        // Add pagination
        $sql .= "  LIMIT %d OFFSET %d";
        
        // Add pagination arguments to our query
        $query_args = array_merge($where_args, [$perPage, $offset]);
        
        // Prepare and execute queries
        $prepared_sql = $wpdb->prepare($sql, $query_args);
        $listings = $wpdb->get_results($prepared_sql);
        
        // Get total for pagination - need to use separate prepare for count query
        $prepared_count_sql = empty($where_args) ? $countSql : $wpdb->prepare($countSql, $where_args);
        $total = $wpdb->get_var($prepared_count_sql);
        
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
        $listing->social_links = is_string($listing->social_links) ? json_decode($listing->social_links, true) : $listing->social_links;
        $listing->category_id = is_string($listing->category_id) ? json_decode($listing->category_id, true) : $listing->category_id;
        $listing->tag_id = is_string($listing->tag_id) ? json_decode($listing->tag_id, true) : $listing->tag_id;
        $listing->tags         = (new Tag())->whereIn('id', $listing->tag_id)->get()->toArray();
        $listing->categories   = (new Category())->whereIn('id', $listing->category_id)->get()->toArray();
    
        return $listing->toArray();
    }
}
