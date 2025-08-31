<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductPrice;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'outlet_id' => 1, //$this->faker->numberBetween(1, 10),
            'barcode' => $this->faker->ean13(),
            'user_id' => 1, //$this->faker->numberBetween(1, 5),
            'product_category_id' => 1, //$this->faker->numberBetween(1, 20),
            'product_unit_id' => 1,  //$this->faker->numberBetween(1, 3),
            'code' => IdGenerator::generate(['table' => 'products', 'field' => 'code', 'length' => 5, 'prefix' => 'P']),
            'name' => $this->faker->words(2, true),
            'slug' => $this->faker->slug,
            'desc' => $this->faker->sentence,
            'capital_price' => $this->faker->randomFloat(2, 1000, 10000),
            'minimum_stock' => $this->faker->numberBetween(50, 200),
            'type_product' => $this->faker->randomElement(['product']),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            ProductPrice::create([
                'product_id' => $product->id,
                'price' => '30000',
                'type' => 'utama',
                'selling_price_id' => 1,
            ]);
        });
    }
}
