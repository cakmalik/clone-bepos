<?php

namespace App\Jobs;

use App\Models\Menu;
use App\Models\Role;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Inventory;
use App\Models\Permission;
use App\Models\UserOutlet;
use App\Models\ProductUnit;
use Illuminate\Support\Str;
use App\Models\ProfilCompany;
use App\Models\UserInventory;
use Illuminate\Bus\Queueable;
use App\Models\CashierMachine;
use App\Models\ProductCategory;
use Database\Seeders\BankSeeder;
use Database\Seeders\MenuSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Log;
use Database\Seeders\SupplierSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\SellingPriceSeeder;
use Illuminate\Queue\InteractsWithQueue;
use Database\Seeders\PaymentMethodSeeder;
use Database\Seeders\CashierMachineSeeder;
use Laravolt\Indonesia\Seeds\CitiesSeeder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Laravolt\Indonesia\Seeds\VillagesSeeder;
use Laravolt\Indonesia\Seeds\DistrictsSeeder;
use Laravolt\Indonesia\Seeds\ProvincesSeeder;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SetupInitializeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $companyName;
    public $selectedProductType;
    public $inventoriesName;
    public $outletsName;

    public function __construct($companyName, $selectedProductType, $inventoriesName, $outletsName)
    {
        $this->companyName = $companyName;
        $this->selectedProductType = $selectedProductType;
        $this->inventoriesName = $inventoriesName;
        $this->outletsName = $outletsName;
    }

    public function handle()
    {
        $this->callDefaultSeeds();
        $this->generateUserAndOutlet();

        // Log::info('Setup Initialization Started', [
        //     'companyName' => $this->companyName,
        //     'selectedProductType' => $this->selectedProductType,
        //     'inventoriesName' => $this->inventoriesName,
        //     'outletsName' => $this->outletsName,
        // ]);

        $this->storeCompany();
        foreach ($this->inventoriesName as $inventory) {
            if ($inventory == '') {
                continue;
            }

            $this->storeInventory($inventory);
        }
        $this->assignUserToInventory();


        Log::info('Setup Initialization Completed');
    }

    private function callDefaultSeeds()
    {
        $menuSeed = new MenuSeeder();
        $menuSeed->run();

        $provinceSeed = new ProvincesSeeder();
        $provinceSeed->run();

        $citySeed = new CitiesSeeder();
        $citySeed->run();

        $districtSeed = new DistrictsSeeder();
        $districtSeed->run();

        $villageSeed = new VillagesSeeder();
        $villageSeed->run();

        $bankSeed = new BankSeeder();
        $bankSeed->run();

        $roleSeeder = new SupplierSeeder();
        $roleSeeder->run();

        $payment_method  = new PaymentMethodSeeder();
        $payment_method->run();

        $sellingPrice = new SellingPriceSeeder();
        $sellingPrice->run();

        $setting = new SettingSeeder();
        $setting->run();
    }

    private function generateUserAndOutlet()
    {
        $u_superadmin = User::create([
            'role_id' => 1,
            'users_name' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@mail.com',
            'password' => Hash::make('123'),
            'pin' => Crypt::encryptString('123456')
        ]);

        foreach ($this->outletsName as $key => $value) {
            $u_spv = User::create([
                'role_id' => 2,
                'users_name' => 'Supervisor ' . ($value),
                'username' => 'spv_' . ($value),
                'email' => 'spv_' . ($value) . '@mail.com',
                'password' => Hash::make('123'),
                'pin' => Crypt::encryptString('123456')
            ]);
            $u_kasir = User::create([
                'role_id' => 3,
                'users_name' => 'kasir_' . ($value),
                'username' => 'kasir_' . ($value),
                'email' => 'kasir_' . ($value) . '@mail.com',
                'password' => Hash::make('123'),
                'pin' => Crypt::encryptString('123456')
            ]);

            if ($value == '') {
                continue;
            }

            $outlet = $this->storeOutlet($value, $key);

            // $u_spv->outlets()->attach($outlet->id);
            // $u_kasir->outlets()->attach($outlet->id);
            // $u_superadmin->outlets()->attach($outlet->id);

            UserOutlet::updateOrCreate([
                'user_id' => $u_spv->id,
                'outlet_id' => $outlet->id,
            ]);

            UserOutlet::updateOrCreate([
                'user_id' => $u_kasir->id,
                'outlet_id' => $outlet->id,
            ]);

            UserOutlet::updateOrCreate([
                'user_id' => $u_superadmin->id,
                'outlet_id' => $outlet->id,
            ]);
        }
    }

    private function storeInventory($name)
    {
        $code = Str::upper(preg_replace('/\s+/', '', $name));

        Inventory::updateOrCreate([
            'name' => $name,
        ], [
            'name' => $name,
            'code' => 'INV-' . $code,
            'type' => 'gudang',
            'is_parent' => 1,
            'is_active' => 1,
        ]);
    }

    private function storeOutlet($name, $key): Outlet
    {
        $code = Str::upper(preg_replace('/\s+/', '', $name));

        $outlet =  Outlet::updateOrCreate([
            'name' => $name,
        ], [
            'name' => $name,
            'code' => 'OUT-' . $code,
            'type' => 'minimarket',
            'is_main' => $key == 0 ? true : false,
            'is_active' => true,
            'phone' => '08123456789',
            'address' => 'Jl. Hayam Wuruk',
        ]);

        $this->outletPermissionSeed($outlet->id, $key);
        $this->productUnitSeed($outlet->id);
        $this->productCategorySeed($outlet->id, $key);
        $this->cashierMachineSeed($outlet->id);

        return $outlet;
    }

    private function storeCompany()
    {
        $data = [
            [
                'image' => null,
                'name' => $this->companyName,
                'email' => preg_replace('/\s+/', '', $this->companyName) . '@gmail.com',
                'address' => 'Depok, Sleman, Daerah Istimewa Yogyakarta 55281',
                'about' => '-',
                'telp' => '-',
                'status' => 'active',
                'product_version' => $this->selectedProductType
            ]
        ];

        foreach ($data as $key => $value) {
            ProfilCompany::create($value);
        }
    }

    private function outletPermissionSeed($outlet_id, $key)
    {
        $menu = Menu::all();

        foreach ($menu as $value) {
            if ($key == 0) {
                Permission::create([
                    'outlet_id' => $outlet_id,
                    'role_id'   => 1,
                    'menu_id'   => $value->id
                ]);
            }
            Permission::create([
                'outlet_id' => $outlet_id,
                'role_id'   => 2,
                'menu_id'   => $value->id
            ]);

            $roleDev = Role::where('role_name', 'DEVELOPER')->first();
            if ($roleDev) {
                Permission::create([
                    'outlet_id' => $outlet_id,
                    'role_id'   => $roleDev->id,
                    'menu_id'   => $value->id
                ]);
            }
        }
    }

    private function productUnitSeed($outlet_id)
    {
        $data = [
            [
                // 'id' => 1,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Pcs',
                'desc' => 'Pcs (Piece) - Satuan Unit Perbuah',
            ],
            [
                // 'id' => 2,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Kg',
                'desc' => 'Kg (Kilogram) - Satuan Berat',
            ],
            [
                // 'id' => 3,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Ltr',
                'desc' => 'Ltr (Liter) - Satuan Volume Cairan',
            ],
            [
                // 'id' => 4,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Mtr',
                'desc' => 'Mtr (Meter) - Satuan Panjang',
            ],
            [
                // 'id' => 5,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Lusin',
                'desc' => 'Lusin - Satuan dalam Kotak',
            ],
            [
                // 'id' => 6,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Box',
                'desc' => 'Box - Kemasannya dalam Kotak',
            ],
            [
                // 'id' => 7,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Pack',
                'desc' => 'Pack - Satuan dalam Kemasan',
            ],
            [
                // 'id' => 8,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Roll',
                'desc' => 'Roll - Satuan Gulungan',
            ],
            [
                // 'id' => 9,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Set',
                'desc' => 'Set - Satuan dalam Paket Set',
            ],
            [
                // 'id' => 10,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Btl',
                'desc' => 'Btl (Botol) - Satuan dalam Botol',
            ],
            [
                // 'id' => 11,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Gr',
                'desc' => 'Gr (Gram) - Satuan Berat Lebih Kecil dari Kilogram',
            ],
            [
                // 'id' => 12,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Cm',
                'desc' => 'Cm (Centimeter) - Satuan Panjang Lebih Kecil dari Meter',
            ],
            [
                // 'id' => 13,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Ml',
                'desc' => 'Ml (Milliliter) - Satuan Volume Cairan Lebih Kecil dari Liter',
            ],
            [
                // 'id' => 14,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Rollmeter',
                'desc' => 'Rollmeter - Satuan panjang pada gulungan',
            ],
            [
                // 'id' => 15,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Slof',
                'desc' => 'Slof - Satuan dalam Kotak',
            ],
            [
                // 'id' => 16,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Ball',
                'desc' => 'Ball - Satuan dalam Kotak',
            ],
            [
                // 'id' => 17,
                'outlet_id' => $outlet_id,
                'user_id' => 1,
                'name' => 'Karton',
                'desc' => 'Karton - Satuan dalam Kotak',
            ],
        ];

        foreach ($data as $key => $value) {
            ProductUnit::create($value);
        }
    }

    protected function productCategorySeed($outlet_id, $key = null)
    {
        $data = [
            // Kategori Utama
            [
                'id' => 1,
                'outlet_id' => $outlet_id,
                'code' => 'PC001',
                'name' => 'Food',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'food',
                'desc' => 'Food category',
            ],
            [
                'id' => 2,
                'outlet_id' => $outlet_id,
                'code' => 'PC002',
                'name' => 'Drink',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'drink',
                'desc' => 'Drink category',
            ],
            [
                'id' => 3,
                'outlet_id' => $outlet_id,
                'code' => 'PC003',
                'name' => 'Snack',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'snack',
                'desc' => 'Snack category',
            ],
            [
                'id' => 4,
                'outlet_id' => $outlet_id,
                'code' => 'PC004',
                'name' => 'Electronic',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'electronic',
                'desc' => 'Electronic category',
            ],
            [
                'id' => 5,
                'outlet_id' => $outlet_id,
                'code' => 'PC005',
                'name' => 'Vegetable',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'vegetable',
                'desc' => 'Vegetable category',
            ],
            [
                'id' => 6,
                'outlet_id' => $outlet_id,
                'code' => 'PC006',
                'name' => 'Meat',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'meat',
                'desc' => 'Meat category',
            ],
            [
                'id' => 7,
                'outlet_id' => $outlet_id,
                'code' => 'PC007',
                'name' => 'Fish',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'fish',
                'desc' => 'Fish category',
            ],
            [
                'id' => 8,
                'outlet_id' => $outlet_id,
                'code' => 'PC008',
                'name' => 'Bread',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'bread',
                'desc' => 'Bread category',
            ],
            [
                'id' => 9,
                'outlet_id' => $outlet_id,
                'code' => 'PC009',
                'name' => 'Dairy',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'dairy',
                'desc' => 'Dairy category',
            ],
            [
                'id' => 10,
                'outlet_id' => $outlet_id,
                'code' => 'PC010',
                'name' => 'Furniture',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'furniture',
                'desc' => 'Furniture category',
            ],
            [
                'id' => 11,
                'outlet_id' => $outlet_id,
                'code' => 'PC011',
                'name' => 'Tobacco',
                'is_parent_category' => true,
                'parent_id' => null,
                'slug' => 'Tobacco',
                'desc' => 'Tobacco category',
            ],
            [
                'id' => 12,
                'outlet_id' => $outlet_id,
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
                'outlet_id' => $outlet_id,
                'code' => 'PC013',
                'name' => 'Chicken',
                'is_parent_category' => false,
                'parent_id' => 6, // Meat
                'slug' => 'chicken',
                'desc' => 'Chicken category',
            ],
            [
                'id' => 14,
                'outlet_id' => $outlet_id,
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
                'outlet_id' => $outlet_id,
                'code' => 'PC015',
                'name' => 'Shampoo',
                'is_parent_category' => false,
                'parent_id' => 12, // Personal Care
                'slug' => 'shampoo',
                'desc' => 'Shampoo category',
            ],
            [
                'id' => 16,
                'outlet_id' => $outlet_id,
                'code' => 'PC016',
                'name' => 'Soap',
                'is_parent_category' => false,
                'parent_id' => 12, // Personal Care
                'slug' => 'soap',
                'desc' => 'Soap category',
            ],
        ];

        foreach ($data as $key => $value) {
            ProductCategory::create($value);
        }
    }

    public function assignUserToInventory()
    {
        $users = User::whereHas('role', function ($query) {
            $query->whereIn('role_name', ['SUPERVISOR', 'SUPERADMIN']);
        })->get();

        $inventories = Inventory::all();

        foreach ($users as $user) {
            foreach ($inventories as $inventory) {
                UserInventory::create([
                    'user_id' => $user->id,
                    'inventory_id' => $inventory->id,
                ]);
            }
        }
    }

    public function cashierMachineSeed($outlet_id)
    {
        CashierMachine::create([
            'name' => 'Kasir 1',
            'outlet_id' => $outlet_id,
            'code' => 'KASIR-1',
        ]);
    }
}
