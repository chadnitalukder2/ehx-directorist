<?php
namespace EhxDirectorist\Hooks\Shortcode;

class ListingShortcode {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        wp_enqueue_style(
            'ehx-directorist-admin',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/css/admin.css',
            [],
            EHX_DIRECTORIST_VERSION
        );
    }

    public function enqueueScripts() {
        wp_enqueue_script(
            'ehx-directorist-app',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/js/listing-app.js',
            ['jquery'],
            null,
            true
        );

        wp_localize_script('ehx-directorist-app', 'EhxDirectoristData', [
            'rest_api' => rest_url('ehx-directorist/v1'),
            'nonce'    => wp_create_nonce('wp_rest'),
        ]);
    }

    public function renderShortcode() {
        // Output the app container
        return '<div id="ehx-directorist-app"></div>';
    }
}
