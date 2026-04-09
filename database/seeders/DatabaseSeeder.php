<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            RisksTableSeeder::class,
            AuditsTableSeeder::class,
            FindingsTableSeeder::class,
            ActionItemsTableSeeder::class,
        ]);
    }
}
