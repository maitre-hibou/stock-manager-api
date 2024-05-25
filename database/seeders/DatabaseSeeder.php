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
            'name' => 'Arnaud P.',
            'email' => 'arnaud@stock-manager.local',
            'role' => Role::ADMIN,
        ]);

        User::factory(5)->create();

        Product::factory(30)->create();
    }
}
