<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\TieredPrices;
use App\Models\CustomerCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportPrice implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Lewati heading dan baris contoh
        $rows->skip(2)->each(function ($row, $index) {
        $barcode = trim($row[0] ?? '');
        $productName = trim($row[1] ?? '');

        $product = Product::where('barcode', $barcode)->first();

        if (!$product && $productName) {
            $product = Product::where('name', $productName)->first();
        }
            if (!$product) {
                Log::warning("Baris " . ($index + 1) . ": Produk dengan barcode '$barcode' tidak ditemukan.");
                return;
            }

            // =============== TIERED PRICES =====================
            for ($i = 0; $i < 4; $i++) {
                $minQty = $row[2 + ($i * 3)] ?? null;
                $maxQty = $row[3 + ($i * 3)] ?? null;
                $price  = $this->normalizePrice($row[4 + ($i * 3)] ?? null);

                if ($minQty && $maxQty && $price) {
                    try {
                        TieredPrices::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'min_qty' => $minQty,
                                'max_qty' => $maxQty,
                            ],
                            [
                                'price' => $price,
                                'user_id' => auth()->id() ?? 1,
                                'outlet_id' => null,
                            ]
                        );
                        Log::info("Tiered price disimpan untuk produk {$product->name} ({$minQty}-{$maxQty}): Rp" . number_format($price, 0, ',', '.'));
                    } catch (\Throwable $e) {
                        Log::error("Gagal menyimpan tiered price (baris " . ($index + 1) . "): " . $e->getMessage());
                    }
                }
            }

            // ============ CUSTOMER CATEGORY PRICES =============
            for ($i = 0; $i < 4; $i++) {
                $categoryName = $row[14 + ($i * 2)] ?? null;
                $price = $this->normalizePrice($row[15 + ($i * 2)] ?? null);

                if ($categoryName && $price) {
                   $category = CustomerCategory::firstOrCreate(
                        ['name' => $categoryName],
                        [
                            'code' => customerCategoryCode(),
                            'slug' => \Str::slug($categoryName),
                            'description' => 'Kategori untuk pelanggan ' . \Str::slug($categoryName), 
                        ]
                    );

                    if (!$category) {
                        Log::warning("Baris " . ($index + 1) . ": Kategori '$categoryName' tidak ditemukan.");
                        continue;
                    }

                    try {
                        ProductPrice::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'customer_category_id' => $category->id,
                            ],
                            [
                                'price' => $price,
                                'type' => 'lain',
                            ]
                        );
                        Log::info("Harga kategori '{$category->name}' disimpan untuk produk {$product->name}: Rp" . number_format($price, 0, ',', '.'));
                    } catch (\Throwable $e) {
                        Log::error("Gagal menyimpan harga kategori (baris " . ($index + 1) . "): " . $e->getMessage());
                    }
                }
            }
        });
    }

    private function normalizePrice($value): ?int
    {
        if (!$value) return null;

        // Bersihkan format: "10.000" → 10000
        $clean = preg_replace('/[^\d]/', '', $value);

        return is_numeric($clean) ? (int) $clean : null;
    }
}
