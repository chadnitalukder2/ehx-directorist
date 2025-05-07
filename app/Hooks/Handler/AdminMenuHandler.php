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

    
  
        $this->enqueue_admin_assets();
    }

    public function render_admin_page() {
        echo '<div id="restaurant-menu-mange-and-order-app"></div>';
    }

    public function enqueue_admin_assets() {
        // if (strpos($hook, 'restaurant-menu-mange-and-order') === false) {
        //     return;
        // }

        wp_enqueue_style(
            'restaurant-menu-mange-and-order-admin',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/css/admin.css',
            [],
            EHX_DIRECTORIST_VERSION
        );

        wp_enqueue_script(
            'restaurant-menu-mange-and-order-admin-js',
            EHX_DIRECTORIST_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            EHX_DIRECTORIST_VERSION,
            true
        );

        wp_localize_script('restaurant-menu-mange-and-order-admin-js', 'EhxDirectoristData', [
            'rest_api' => rest_url('easy-restaurant-manage/v1'),
            'nonce' => wp_create_nonce('wp_rest')
        ]);
    }
}