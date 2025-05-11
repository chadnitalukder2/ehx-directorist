<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Category;
use EhxDirectorist\Models\Listing;
use EhxDirectorist\Services\Arr;

class ListingResource {
    public static function store($data) {
        $data['category_id'] = json_encode($data['category_id']);
        $data['tag_id'] = json_encode($data['tag_id']);
        $data['meta'] = json_encode($data['meta']);

        $id = Arr::get($data, 'id', null);

        if($id) {
            return self::update($id, $data);
        }

        return Listing::create($data);
     
    }

    public static function update($id, $data) {
        //return Category::where('id', $id)->update($category);
        $categoryModel = new Listing();
        $category = $categoryModel->find($id);
      
        if ($category) {
           
            $category->update($data, $id);
       
            return true; 
        }
    
        return false;
    }

    public static function delete($id) {
        $categoryModel = new Category();
        $category = $categoryModel->find($id);
    
        if ($category) {
            $category->delete($id);
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
        $categories = (new Listing())->paginate($perPage, $page, $search);

        // $categories = (new Category)->all();
        // $data = array_map(fn($cat) => $cat->toArray(), $categories);
        return  $categories;
    }

}