<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Root seeder — orchestrates the order in which all seeders are run.
 * Run with: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BookSeeder::class,
        ]);
    }
}
