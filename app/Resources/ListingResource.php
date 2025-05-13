<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Category;
use EhxDirectorist\Models\Listing;
use EhxDirectorist\Models\Post;
use EhxDirectorist\Services\Arr;

class ListingResource {
    public static function store($data) {
        
        $data['category_id'] = json_encode($data['category_id']);
        $data['tag_id'] = json_encode($data['tag_id']);
        $data['meta'] = json_encode($data['meta']);


        return Listing::create($data);
     
    }

    public static function update($data, $id) {
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

    public static function delete($id) {
        $listingModel = new Listing();
        $listing = $listingModel->find($id);
    
        if ($listing) {
            $listing->delete($id);
            return true; 
        }
    
        return false;
    }

    public static function get($id) {
        $listing = (new Listing())->find($id);
        if (!$listing) {
            return null;
        }
        $listing->meta = json_decode($listing->meta);
        $listing->category_id = json_decode($listing->category_id);
        $listing->tag_id = json_decode($listing->tag_id);
        return $listing->toArray();
    }

    public static function getAll($perPage, $page, $search) {
        $listingModel = new Listing();
    
        // This should return something like ['data' => [...], 'pagination' => [...]]
        $listings = $listingModel->paginate($perPage, $page, $search);
    
        foreach ($listings['data'] as &$listing) {
        
            if (isset($listing->meta)) {
                // If the meta field is a string, decode it into an array
                $listing->meta = json_decode($listing->meta, true);
            }
            dd($listing->category_id);
            if (isset($listing->category_id)) {
              
                $listing->category_id = json_decode($listing->category_id, true);
            }
    
            if (isset($listing->tag_id)) {
                // Ensure the value is JSON-decoded properly
                $listing->tag_id = json_decode($listing->tag_id, true);
            }
        }
    dd($listing, 'return');
        // return $listings;
    }

}