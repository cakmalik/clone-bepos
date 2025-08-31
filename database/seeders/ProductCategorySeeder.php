<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Kategori Utama
            [
                'id' => 1,
                'outlet_id' => 1,
                'code' => 'PC001',
                'name' => 'Food',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'food',
                'desc' => 'Food category',
            ],
            [
                'id' => 2,
                'outlet_id' => 1,
                'code' => 'PC002',
                'name' => 'Drink',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'drink',
                'desc' => 'Drink category',
            ],
            [
                'id' => 3,
                'outlet_id' => 1,
                'code' => 'PC003',
                'name' => 'Snack',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'snack',
                'desc' => 'Snack category',
            ],
            [
                'id' => 4,
                'outlet_id' => 1,
                'code' => 'PC004',
                'name' => 'Electronic',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'electronic',
                'desc' => 'Electronic category',
            ],
            [
                'id' => 5,
                'outlet_id' => 1,
                'code' => 'PC005',
                'name' => 'Vegetable',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'vegetable',
                'desc' => 'Vegetable category',
            ],
            [
                'id' => 6,
                'outlet_id' => 1,
                'code' => 'PC006',
                'name' => 'Meat',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'meat',
                'desc' => 'Meat category',
            ],
            [
                'id' => 7,
                'outlet_id' => 1,
                'code' => 'PC007',
                'name' => 'Fish',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'fish',
                'desc' => 'Fish category',
            ],
            [
                'id' => 8,
                'outlet_id' => 1,
                'code' => 'PC008',
                'name' => 'Bread',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'bread',
                'desc' => 'Bread category',
            ],
            [
                'id' => 9,
                'outlet_id' => 1,
                'code' => 'PC009',
                'name' => 'Dairy',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'dairy',
                'desc' => 'Dairy category',
            ],
            [
                'id' => 10,
                'outlet_id' => 1,
                'code' => 'PC010',
                'name' => 'Furniture',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'furniture',
                'desc' => 'Furniture category',
            ],
            [
                'id' => 11,
                'outlet_id' => 1,
                'code' => 'PC011',
                'name' => 'Tobacco',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'Tobacco',
                'desc' => 'Tobacco category',
            ],
            [
                'id' => 12,
                'outlet_id' => 1,
                'code' => 'PC012',
                'name' => 'Personal Care',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'Personal Care',
                'desc' => 'Personal Care category',
            ],

            // Subkategori untuk Meat
            [
                'id' => 13,
                'outlet_id' => 1,
                'code' => 'PC013',
                'name' => 'Chicken',
                'is_parent_category' => false,
                'parent_id' => 6, // Meat
                'slug' => 'chicken',
                'desc' => 'Chicken category',
            ],
            [
                'id' => 14,
                'outlet_id' => 1,
                'code' => 'PC014',
                'name' => 'Beef',
                'is_parent_category' => false,
                'parent_id' => 6, // Meat
                'slug' => 'beef',
                'desc' => 'Beef category',
            ],

            //Subkategori untuk Personal Care
            [
                'id' => 15,
                'outlet_id' => 1,
                'code' => 'PC015',
                'name' => 'Shampoo',
                'is_parent_category' => false,
                'parent_id' => 12, // Personal Care
                'slug' => 'shampoo',
                'desc' => 'Shampoo category',
            ],
            [
                'id' => 16,
                'outlet_id' => 1,
                'code' => 'PC016',
                'name' => 'Soap',
                'is_parent_category' => false,
                'parent_id' => 12, // Personal Care
                'slug' => 'soap',
                'desc' => 'Soap category',
            ],
            //bundling
            [
                'id' => 17,
                'outlet_id' => 1,
                'code' => 'PC017',
                'name' => 'Bundling',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'bundling',
                'desc' => 'Bundling category',
            ],
        ];

        foreach ($data as $key => $value) {
            ProductCategory::create($value);
        }
    }
}
