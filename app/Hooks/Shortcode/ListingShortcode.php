<?php
namespace EhxDirectorist\Hooks\Shortcode;

class ListingShortcode {

    public function renderShortcode() {
        // Enqueue the necessary scripts and styles for the shortcode
        wp_enqueue_script('ehx-directorist-app', EHX_DIRECTORIST_PLUGIN_URL . 'assets/js/listing-app.js', ['jquery'], null, true);
        return '<div id="ehx-directorist-app"></div>';
    }

}