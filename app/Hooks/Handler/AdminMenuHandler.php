<?php
namespace EhxDirectorist\Hooks\Handler;

class AdminMenuHandler {
      public function add_admin_menu() {
        global $submenu;
        add_menu_page(
            'ehx-directorist',
            'EHX Directorist',
            'manage_options',
            'ehx-directorist.php',
            array($this, 'render_admin_page'),
            'dashicons-editor-code',
            25
        );

        $submenu['ehx-directorist.php']['dashboard'] = array(
            'Dashboard',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/',
        );
        $submenu['ehx-directorist.php']['listing-directory'] = array(
            'Listing Directory',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/listing-directory',
        );

        $submenu['ehx-directorist.php']['tags'] = array(
            'Tags',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/tags',
        );

        $submenu['ehx-directorist.php']['categories'] = array(
            'Categories',
            'manage_options',
            'admin.php?page=ehx-directorist.php#/categories',
        );
        $this->enqueue_admin_assets();
    }

    public function render_admin_page() {
        echo '<div id="ehx-directorist-app"></div>';
    }

    public function enqueue_admin_assets() {
        // if (strpos($hook, 'ehx-directorist') === false) {
        //     return;
        // }

        wp_enqueue_style(
            'ehx-directorist-admin',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/css/admin.css',
            [],
            EHX_DIRECTORIST_VERSION
        );

        wp_enqueue_script(
            'ehx-directorist-admin-js',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            EHX_DIRECTORIST_VERSION,
            true
        );

        wp_localize_script('ehx-directorist-admin-js', 'EhxDirectoristData', [
            'rest_api' => rest_url('ehx-directorist/v1'),
            'nonce' => wp_create_nonce('wp_rest')
        ]);
    }
}