<?php

namespace EhxDirectorist\Hooks;
use EhxDirectorist\Hooks\Handler\AdminMenuHandler;

class Actions {
    public function __construct() {
        $this->register_hooks();
    }
    private function register_hooks() {
        $admin_menu_handler = new AdminMenuHandler();
        add_action('admin_menu', [$admin_menu_handler, 'add_admin_menu']);

        remove_all_actions('admin_notices');

        wp_enqueue_script(
            'ehx-google-maps-api',
            'https://maps.googleapis.com/maps/api/js?key=AIzaSyAdBAaTcZW8-4MCuVwzc7mcGS0OasoplgU&libraries=places',
            [],
            null,
            true
        );
        
    }
}