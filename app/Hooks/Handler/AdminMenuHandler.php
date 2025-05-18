<?php

namespace EhxDirectorist\Hooks\Handler;

class AdminMenuHandler
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
    }

    public function add_admin_menu()
    {
        global $submenu;

        add_menu_page(
            'ehx-directorist',
            'EHX Directorist',
            'manage_options',
            'ehx-directorist.php',
            [$this, 'render_admin_page'],
            'dashicons-editor-code',
            25
        );

        $submenu['ehx-directorist.php']['dashboard'] = [
            'Dashboard',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/',
        ];

        $submenu['ehx-directorist.php']['directory-listing'] = [
            'Directory Listing',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/directory-listing',
        ];

        $submenu['ehx-directorist.php']['tags'] = [
            'Tags',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/tags',
        ];

        $submenu['ehx-directorist.php']['categories'] = [
            'Categories',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/categories',
        ];
        $submenu['ehx-directorist.php']['settings'] = [
            'Settings',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/settings',
        ];
    }

    public function render_admin_page()
    {
        echo '<div id="ehx-directorist-app"></div>';
    }

    public function enqueue_admin_assets($hook)
    {
        // Optional: restrict loading to only your plugin page
        if (strpos($hook, 'ehx-directorist') === false) {
            return;
        }
        wp_enqueue_editor();


        wp_enqueue_style(
            'ehx-directorist-admin',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/css/admin.css',
            [],
            EHX_DIRECTORIST_VERSION
        );

        wp_enqueue_script(
            'ehx-directorist-admin-js',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-editor', 'wp-components', 'wp-element', 'wp-data'],
            EHX_DIRECTORIST_VERSION,
            true
        );
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
        wp_enqueue_script('jquery');
        wp_enqueue_media();
        wp_enqueue_script('wp-tinymce');
        wp_enqueue_script('wp-editor');

        wp_localize_script('ehx-directorist-admin-js', 'EhxDirectoristData', [
            'rest_api' => rest_url('ehx-directorist/v1'),
            'nonce'    => wp_create_nonce('wp_rest'),
        ]);
    }
}
