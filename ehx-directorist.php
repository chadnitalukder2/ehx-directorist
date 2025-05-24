<?php
/**
 * Updated main plugin file with Elementor widget integration
 * File: ehx-directorist.php (your main plugin file)
 */

/**
 * Plugin Name: Ehx Directorist
 * Plugin URI: https://example.com/ehx-irectorist
 * Description: Description of the plugin goes here.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: ehx-directorist
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('EHX_DIRECTORIST_VERSION')) {
    define('EHX_DIRECTORIST_VERSION', '1.0.0');
}

if (!defined('EHX_DIRECTORIST_PLUGIN_DIR')) {
    define('EHX_DIRECTORIST_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('EHX_DIRECTORIST_PLUGIN_URL')) {
    define('EHX_DIRECTORIST_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Load API routes
use EhxDirectorist\Http\Router\API_Router;
use EhxDirectorist\Http\Router\Route;

// Autoloader
require_once EHX_DIRECTORIST_PLUGIN_DIR . 'vendor/autoload.php';

// Register API routes on REST API initialization
add_action('rest_api_init', function () {
    Route::register();
    API_Router::register_routes();
});

// Initialize the plugin
function EHX_DIRECTORIST_init() {
    \EhxDirectorist\Core\Plugin::instance();
}

add_action('init', function () {
    wp_enqueue_script(
        'ehx-google-maps-api',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyAdBAaTcZW8-4MCuVwzc7mcGS0OasoplgU&libraries=places',
        [],
        null,
        true 
    );
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
});

/**
 * Check if Elementor is active and initialize widgets
 */
function ehx_directorist_elementor_init() {
    // Check if Elementor is loaded
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', 'ehx_directorist_elementor_fail_load');
        return;
    }

    // Register Elementor widgets
    add_action('elementor/widgets/register', 'ehx_directorist_register_widgets');
}

/**
 * Show admin notice if Elementor is not active
 */
function ehx_directorist_elementor_fail_load() {
    $message = sprintf(
        esc_html__('"%1$s" Elementor widgets require "%2$s" to be installed and activated.', 'ehx-directorist'),
        '<strong>' . esc_html__('EHX Directorist', 'ehx-directorist') . '</strong>',
        '<strong>' . esc_html__('Elementor', 'ehx-directorist') . '</strong>'
    );
    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
}

/**
 * Register Elementor widgets
 */
function ehx_directorist_register_widgets($widgets_manager) {
    // Register the listings widget
    $widgets_manager->register(new \EhxDirectorist\Modules\Widgets\Directorist_Listings_Widget());
}

// Initialize Elementor widgets after plugins are loaded
add_action('plugins_loaded', 'ehx_directorist_elementor_init');

// Hook into WordPress init
add_action('plugins_loaded', 'EHX_DIRECTORIST_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    require_once EHX_DIRECTORIST_PLUGIN_DIR . 'app/Database/Migrator.php';
    $installer = new \EhxDirectorist\Database\Migrator();
    $installer->migrate();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
});

/**
 * Fires after WordPress has finished loading but before any headers are sent.
 */
function action_init() : void {
    // Your init code here
}