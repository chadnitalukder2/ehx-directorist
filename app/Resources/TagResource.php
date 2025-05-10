<?php
namespace EhxDirectorist\Resources;

use EhxDirectorist\Models\Tag;

class TagResource {
    public static function store($data) {
        return Tag::create($data);
    }

    public static function update($data, $id) {
        $tagModel = new Tag();
        $tag = $tagModel->find($id);
      
        if ($tag) {
            $tag->update($data, $id);
            return true; 
        }
        return false;
    }

    public static function getAll($perPage, $page, $search) {
        $data = (new Tag)->paginate($perPage, $page, $search);
        return $data;
    }

    public static function delete($id) {
        $tagModel = new Tag();
        $tag = $tagModel->find($id);
    
        if ($tag) {
            $tag->delete($id);
            return true; 
        }
        return false;
    }

}