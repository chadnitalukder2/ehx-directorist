<?php
namespace EhxDirectorist\Database\Migrations;

use wpdb;

class CreateReviewsTable  {
    public static function up() {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = "{$wpdb->prefix}ehxd_reviews";

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INT PRIMARY KEY AUTO_INCREMENT,
            directory_list_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            rating INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        dbDelta($sql);
    }
}