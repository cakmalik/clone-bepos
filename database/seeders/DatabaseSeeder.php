<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Discount;
use App\Models\ProductPrice;
use App\Models\SellingPrice;
use App\Models\ProductSupplier;
use Illuminate\Database\Seeder;
use Database\Seeders\BankSeeder;
use Illuminate\Support\Facades\Hash;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Laravolt\Indonesia\Seeds\VillagesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([
            // MenuSeeder::class,
            RoleSeeder::class,
            // SetupFirstTimeSeeder::class,

            UserSeeder::class,
            MenuSeeder::class,
            PermissionSeeder::class,
            OutletSeeder::class,
            InventorySeeder::class,
            ProductUnitSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            ProductPriceSeeder::class,
            ProductStockSeeder::class,
            SupplierSeeder::class,
            ProvincesSeeder::class,
            CitiesSeeder::class,
            DistrictsSeeder::class,
            VillagesSeeder::class,
            CustomerCategorySeeder::class,
            // CustomerSeeder::class,
            UserOutletSeeder::class,
            UserInventorySeeder::class,
            SellingPriceSeeder::class,
            // ProductStockHistorySeeder::class,
            PaymentMethodSeeder::class,
            CashierMachineSeeder::class,
            // PurchaseSeeder::class,
            ProfilCompanySeeder::class,
            SettingSeeder::class,
            JournalAccountTypeSeeder::class,
            JournalAccountSeeder::class,
            JournalTypeSeeder::class,
            DiscountSeeder::class,
            TablesSeeder::class,
            TIeredPriceSeeder::class,
            ProductSupplierSeeder::class,
            BankSeeder::class,
            MembershipsTableSeeder::class,
            LoyaltySettingsSeeder::class,
            LoyaltyPlusQtySeeder::class,
            // update price by category customer
            // ProductPriceByCustomerCategorySeeder::class,
            ProductBundleSeeder::class
        ]);
    }
}
