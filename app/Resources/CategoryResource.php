<?php
namespace EhxDirectorist\Resources;
use EhxDirectorist\Models\Category;

class CategoryResource {
    public static function store($category) {
        return Category::create($category);
     
    }

    public static function update($data, $id) {
        //return Category::where('id', $id)->update($category);
        $categoryModel = new Category();
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
     //   return Category::find($id);
    }

    public static function getAll($perPage, $page, $search) {
        $categories = (new Category)->paginate($perPage, $page, $search);

        // $categories = (new Category)->all();
        // $data = array_map(fn($cat) => $cat->toArray(), $categories);
        return  $categories;
    }

}