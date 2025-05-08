<?php

namespace EhxDirectorist\Http\Router;

use EhxDirectorist\Http\Controllers\CategoryController;
use EhxDirectorist\Http\Controllers\TagController;
use WP_REST_Request;

class Route {
    public static function register() {
        API_Router::get('/getAllCategories', [CategoryController::class, 'getAllCategories']);
        API_Router::post('/postCategory', [CategoryController::class, 'storeCategory']);
        API_Router::post('/deleteCategory/{id}', [CategoryController::class, 'deleteCategory']);
        API_Router::post('/updateCategory/{id}', [CategoryController::class, 'updateCategory']);

        API_Router::get('/getAllTags', [TagController::class, 'getAllTags']);
        API_Router::post('/postTag', [TagController::class, 'storeTag']);
    } 
}
