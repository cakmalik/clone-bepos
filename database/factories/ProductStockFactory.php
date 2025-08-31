<?php

namespace Database\Factories;

use App\Models\ProductStock;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class ProductStockFactory extends Factory
{
    protected $model = ProductStock::class;

    public function definition()
    {
        return [
            'product_id' => function () {
                return \App\Models\Product::factory()->create()->id;
            },
            'outlet_id' => $this->faker->numberBetween(1, 10),
            'stock_current' => $this->faker->numberBetween(50, 200),
        ];
    }

    public function batched(int $batchSize = 1000): array
    {
        $totalEntries = 100000;
        $batches = [];

        for ($i = 0; $i < $totalEntries; $i += $batchSize) {
            $count = min($batchSize, $totalEntries - $i);

            $batches[] = $this->times($count)->create()->toArray();
        }

        return $batches;
    }
}
