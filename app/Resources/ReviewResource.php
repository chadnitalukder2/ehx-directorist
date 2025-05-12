<?php
namespace EhxDirectorist\Resources;

use EhxDirectorist\Models\Review;

class ReviewResource {
    public static function store($data) {
        return Review::create($data);
    }

    public static function update($data, $id) {
        $tagModel = new Review();
        $tag = $tagModel->find($id);
      
        if ($tag) {
            $tag->update($data, $id);
            return true; 
        }
        return false;
    }

    public static function getAll($perPage, $page, $search) {
        $data = (new Review)->paginate($perPage, $page, $search);
        return $data;
    }

    public static function delete($id) {
        $tagModel = new Review();
        $tag = $tagModel->find($id);
    
        if ($tag) {
            $tag->delete($id);
            return true; 
        }
        return false;
    }

}