<?php

namespace EhxDirectorist\Hooks;
use EhxDirectorist\Hooks\Handler\AdminMenuHandler;
use EhxDirectorist\Hooks\Shortcode\ListingShortcode; 

class Actions {
    public function __construct() {
        $this->register_hooks();
    }
    private function register_hooks() {
        $admin_menu_handler = new AdminMenuHandler();
        $listing_shortcode = new ListingShortcode();
        add_action('admin_menu', [$admin_menu_handler, 'add_admin_menu']);
        add_shortcode( 'ehx_directorist_listings', [$listing_shortcode, 'renderShortcode'] );
        add_shortcode( 'ehxd_listing_details', [$this, 'renderShortcode'] );
        remove_all_actions('admin_notices');
        
    }
}