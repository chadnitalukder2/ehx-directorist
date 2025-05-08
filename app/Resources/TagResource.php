<?php
namespace EhxDirectorist\Resources;

use EhxDirectorist\Models\Tag;

class TagResource {
    public static function store($data) {
        return Tag::create($data);
    }

    public static function getAll() {
        $categories = (new Tag)->all();
        $data = array_map(fn($cat) => $cat->toArray(), $categories);
        return $data;
    }

}