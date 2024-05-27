<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Xp\StockManager\Security\Authorization\Domain\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'The Administrator',
            'email' => 'admin@stock-manager.local',
            'role' => Role::ADMIN,
        ]);

        User::factory()->create([
            'name' => 'A Random User',
            'email' => 'user@stock-manager.local',
            'role' => Role::USER,
        ]);

        Product::factory(30)->create();
    }
}
