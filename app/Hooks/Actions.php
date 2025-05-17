<?php

namespace EhxDirectorist\Hooks;

use EhxDirectorist\Hooks\Handler\AdminMenuHandler;
use EhxDirectorist\Hooks\Shortcode\ListingShortcode;
use EhxDirectorist\Http\View;
use EhxDirectorist\Resources\ListingResource;

class Actions
{
    public function __construct()
    {
        $this->register_hooks();
    }
    private function register_hooks()
    {
        $admin_menu_handler = new AdminMenuHandler();
        $listing_shortcode = new ListingShortcode();
        add_action('admin_menu', [$admin_menu_handler, 'add_admin_menu']);
        add_shortcode('ehx_directorist_listings', [$listing_shortcode, 'renderShortcode']);
        add_shortcode('ehxd_listing_details', [$this, 'renderShortcodeDetails']);
        remove_all_actions('admin_notices');
    }
    public function renderShortcodeDetails($attr)
    {
        $id = $attr['id'];
        if (!$id) {
            return 'Id is required';
        }
        $listingData = ListingResource::getPost($id);

        wp_enqueue_script(
            'google-maps',
            'https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY',
            array(),
            null,
            true
        );
     
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
        wp_enqueue_style('ehxd_listing_details', EHX_DIRECTORIST_PLUGIN_URL . 'assets/css/listing-details.css', [], EHX_DIRECTORIST_VERSION);
        wp_enqueue_script('ehxd_listing_details_js', EHX_DIRECTORIST_PLUGIN_URL . 'assets/js/listing-details.js', array('jquery'), EHX_DIRECTORIST_VERSION, false);
        wp_localize_script('ehxd_listing_details_js', 'ehxdMapData', [
            'latitude'  => $listingData['latitude'],
            'longitude' => $listingData['longitude'],
            'address' => $listingData['address'],
            'title'     => $listingData['name'],
        ]);
        ob_start();
        View::render('ListingDetails/ListingDetails', [
            'data' => $listingData,
        ]);

        return ob_get_clean();
    }
}
