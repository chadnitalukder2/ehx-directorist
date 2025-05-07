<?php
namespace EhxDirectorist\Database\Migrations;

use wpdb;

class CreateDirectoryBuilderTable {
    public static function up() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = "{$wpdb->prefix}ehxd_directory_builder";

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT ,
            status VARCHAR(50) NOT NULL DEFAULT 'active',
            type VARCHAR(50) NOT NULL,
            meta VARCHAR(255),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        dbDelta($sql);
    }
    
}
