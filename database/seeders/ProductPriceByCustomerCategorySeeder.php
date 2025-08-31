<?php

namespace Database\Seeders;

use App\Models\SellingPrice;
use Illuminate\Database\Seeder;
use App\Models\CustomerCategory;
use App\Models\ProductPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductPriceByCustomerCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer_category = CustomerCategory::where('name', 'Premium')->first();
        $product_prices = ProductPrice::where('type', 'utama')->get();

        $product_prices->each(function ($pp) use ($customer_category) {
            ProductPrice::create([
                'product_id' => $pp->product_id,
                'customer_category_id' => $customer_category->id,
                'price' => $pp->price - 3000,
                'type' => 'lain',
            ]);
        });



        $customer_category_2 = CustomerCategory::where('name', 'Regular')->first();
        $product_prices = ProductPrice::where('type', 'utama')->get();

        $product_prices->each(function ($pp) use ($customer_category_2) {
            ProductPrice::create([
                'product_id' => $pp->product_id,
                'customer_category_id' => $customer_category_2->id,
                'price' => $pp->price - 1000,
                'type' => 'lain',
            ]);
        });
    }
}
