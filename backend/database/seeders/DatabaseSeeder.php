<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            // Comment out seeders that don't exist yet
            // InventoryCategorySeeder::class,
            // InventorySeeder::class,
            // RentalSeeder::class,
            // SettingSeeder::class,
        ]);
    }
}
