<?php
namespace EhxDirectorist\Database;

use EhxDirectorist\Database\Migrations\CreateCategoriesTable;
use EhxDirectorist\Database\Migrations\CreateDirectoryBuilderTable;
use EhxDirectorist\Database\Migrations\CreateDirectoryListingsTable;
use EhxDirectorist\Database\Migrations\CreateReviewsTable;
use EhxDirectorist\Database\Migrations\CreateTagTable;

class Migrator {
    protected $migrations = [
        CreateCategoriesTable::class,
        CreateTagTable::class,
        CreateDirectoryListingsTable::class,
        CreateDirectoryBuilderTable::class,
        CreateReviewsTable::class,
    ];

    public function migrate() {
        
        foreach ($this->migrations as $migration) {
            if (class_exists($migration) && method_exists($migration, 'up')) {
                call_user_func([$migration, 'up']);
            }
        }
    }
}