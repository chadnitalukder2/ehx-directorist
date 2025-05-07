<?php
namespace EhxDirectorist\Hooks\Handler;

class AdminMenuHandler {
      public function add_admin_menu() {
        global $submenu;
        add_menu_page(
            'ehx-directoristr',
            'EHX Directoristr',
            'manage_options',
            'ehx-directoristr.php',
            array($this, 'render_admin_page'),
            'dashicons-editor-code',
            25
        );

        $submenu['ehx-directoristr.php']['dashboard'] = array(
            'Dashboard',
            'manage_options',
            'admin.php?page=ehx-directoristr.php#/',
        );

        $submenu['ehx-directoristr.php']['tags'] = array(
            'Tags',
            'manage_options',
            'admin.php?page=ehx-directoristr.php#/tags',
        );

        $submenu['ehx-directoristr.php']['categories'] = array(
            'Categories',
            'manage_options',
            'admin.php?page=ehx-directoristr.php#/categories',
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