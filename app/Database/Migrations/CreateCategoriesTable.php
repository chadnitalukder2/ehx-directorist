<?php
namespace EhxDirectorist\Database\Migrations;

use wpdb;

class CreateCategoriesTable {
    public static function up() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = "{$wpdb->prefix}ehxd_categories";

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255),
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        dbDelta($sql);
    }
    
}
