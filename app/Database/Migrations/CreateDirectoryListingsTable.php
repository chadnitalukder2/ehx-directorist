<?php

namespace EhxDirectorist\Database\Migrations;

use wpdb;

class CreateDirectoryListingsTable
{
    public static function up()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $table = "{$wpdb->prefix}ehxd_directory_listing";

        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INT PRIMARY KEY AUTO_INCREMENT,
            directory_builder_id INT NOT NULL,
            category_id JSON NOT NULL,
            tag_id JSON NOT NULL,
            post_id INT NOT NULL,
            post_url VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL, 
            website_url VARCHAR(255) NOT NUll,
            address VARCHAR(255)  NOT NULL,
            latitude FLOAT(10, 6)  ,
            longitude FLOAT(10, 6)  ,  
            postal_code VARCHAR(20)  , 
            city VARCHAR(255) ,
            logo VARCHAR(255)  NOT NULL,
            image VARCHAR(255),
            short_description TEXT ,
            description TEXT,
            social_links JSON,
            meta JSON,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) $charset_collate;";

        dbDelta($sql);
    }
}
