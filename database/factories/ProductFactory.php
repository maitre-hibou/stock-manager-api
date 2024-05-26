<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Xp\StockManager\Catalog\Domain\ProductInterface;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => sprintf('%s %s %s',fake()->randomElement(['Big', 'Medium', 'Small', 'Tiny']), fake()->colorName(), ucfirst(fake()->citySuffix())),
            'description' => fake()->randomElement([null, fake()->text()]),
            'price' => fake()->numberBetween(100, 999900),
            'vat' => fake()->randomElement([ProductInterface::VAT_20, ProductInterface::VAT_10, ProductInterface::VAT_55]),
        ];
    }


}
