<?php

use App\Http\Controllers\API\Minimarket\CashflowController;
use App\Http\Controllers\Controller;
use App\Http\Livewire\Setting\Report as SettingReport;
use Illuminate\Support\Facades\Route;
use App\Jobs\GenerateStockValueReport;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupplierController;
use App\Http\Livewire\Developer\First\Setup;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LiveSalesController;
use App\Http\Controllers\SettingProductStock;
use App\Http\Controllers\CashMasterController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ReturSalesController;
use App\Http\Livewire\Report\StockValueReport;
use App\Http\Controllers\CashProofInController;
use App\Http\Controllers\DebtPaymentController;
use App\Http\Controllers\OutletTableController;
use App\Http\Controllers\ProductUnitController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\CashProofOutController;
use App\Http\Controllers\LoyaltyPointController;
use App\Http\Controllers\ProductPriceController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\TieredPricesController;
use App\Http\Controllers\CashflowCloseController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockMutationController;
use App\Http\Controllers\CashierMachineController;
use App\Http\Controllers\InvoicePaymentController;
use App\Http\Controllers\ProfileCompanyController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\ReportLabaRugiController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductDiscountController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Livewire\Developer\First\CompleteSeed;
use App\Http\Controllers\Accounting\StockController;
use App\Http\Controllers\CustomerCategoryController;
use App\Http\Controllers\Accounting\LedgerController;
use App\Http\Controllers\JournalAdjustmentController;
use App\Http\Controllers\JournalReturSalesController;
use App\Http\Controllers\PurchaseReceptionController;
use App\Http\Controllers\Report\DebtReportController;
use App\Http\Controllers\Accounting\BalanceController;
use App\Http\Controllers\Report\SalesReportController;
use App\Http\Controllers\JournalStockAccountController;
use App\Http\Controllers\ProductSellingPriceController;
use App\Http\Controllers\ProductStockHistoryController;
use App\Http\Controllers\PurchaseRequisitionController;
use App\Http\Controllers\StockMutationRewardController;
use App\Http\Controllers\Accounting\JournalPOController;
use App\Http\Controllers\JournalReturPurchaseController;
use App\Http\Controllers\Report\PaymentReportController;
use App\Http\Controllers\Accounting\ProfitLossController;
use App\Http\Controllers\Accounting\JournalTypeController;
use App\Http\Controllers\Report\StockValueReportController;
use App\Http\Controllers\Report\DebtPaymentReportController;
use App\Http\Controllers\Report\SalesReturnReportController;
use App\Http\Controllers\Report\StockOpnameReportController;
use App\Http\Controllers\Report\StockOutletReportController;
use App\Http\Controllers\Accounting\JournalAccountController;
use App\Http\Controllers\Accounting\JournalClosingController;
use App\Http\Controllers\Report\PurchaseOrderReportController;
use App\Http\Controllers\Report\StockMutationReportController;
use App\Http\Controllers\Report\PurchaseReturnReportController;
use App\Http\Controllers\Report\StockAdjustmentReportController;
use App\Http\Controllers\StockMutationInventoryOutletController;
use App\Http\Controllers\Accounting\JournalAccountTypeController;
use App\Http\Controllers\Accounting\JournalTransactionController;
use App\Http\Controllers\Report\PurchaseReceptionReportController;
use App\Http\Controllers\Report\StockOutletConsolidationController;
use App\Http\Controllers\Report\StockInventoryConsolidationController;
use App\Http\Controllers\API\ProductController as APIProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/testmenu', [Controller::class, 'index']);

    Route::post('/profile', [AuthController::class, 'profile_users']);
    Route::post('/profile_password', [AuthController::class, 'profile_password']);
    Route::get('/search-product', [APIProductController::class, 'searchProduct']);

    Route::middleware(['MenuSecure:Dashboard', 'SetDynamicTitle'])->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('view');
        Route::get('/dashboard', [HomeController::class, 'index'])->name('view.dashboard');
        Route::get('/dashboard/data', [HomeController::class, 'getDataDashboard']);

        Route::get('/dashboard/top-product', [HomeController::class, 'topProduct'])->name('dashboard.top-product');
        Route::get('/dashboard/top-product-data', [HomeController::class, 'topProductData'])->name('dashboard.top-product-data');

        Route::get('/dashboard/top-category', [HomeController::class, 'topCategory'])->name('dashboard.top-category');
        Route::get('/dashboard/top-category-data', [HomeController::class, 'topCategoryData'])->name('dashboard.top-category-data');

        Route::get('/dashboard/top-customer', [HomeController::class, 'topCustomer'])->name('dashboard.top-customer');
        Route::get('/dashboard/top-customer-data', [HomeController::class, 'topCustomerData'])->name('dashboard.top-customer-data');

        Route::get('/dashboard/top-cashier', [HomeController::class, 'topCashier'])->name('dashboard.top-cashier');
        Route::get('/dashboard/top-cashier-data', [HomeController::class, 'topCashierData'])->name('dashboard.top-cashier-data');

        Route::get('/dashboard/top-payment-method', [HomeController::class, 'topPaymentMethod'])->name('dashboard.top-payment-method');
        Route::get('/dashboard/top-payment-method-data', [HomeController::class, 'topPaymentMethodData'])->name('dashboard.top-payment-data');

        Route::get('/dashboard/least-sold-product', [HomeController::class, 'leastSoldProduct'])->name('dashboard.least-sold-product');
        Route::get('/dashboard/least-sold-product-data', [HomeController::class, 'leastSoldProductData'])->name('dashboard.least-sold-product-data');

        Route::get('/dashboard/out-of-stock', [HomeController::class, 'outOfStock'])->name('dashboard.out-of-stock');

        // Route::get('/live-sales', [LiveSalesController::class, 'index'])->name('view.live-sales');
    });

    //Route Supplier
    Route::middleware(['MenuSecure:Supplier', 'SetDynamicTitle'])->group(function () {
        Route::get('/supplier', [SupplierController::class, 'index'])->name('supplier.index');
        Route::get('/supplier/create', [SupplierController::class, 'create'])->name('supplier.create');
        Route::get('/supplier/{id}', [SupplierController::class, 'show'])->name('supplier.show');
        Route::post('/supplier/store', [SupplierController::class, 'store'])->name('supplier.store');
        Route::delete('/supplier/{id}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
        Route::get('/supplier/edit/{id}', [SupplierController::class, 'edit'])->name('supplier.edit');
        Route::put('/supplier/update/{id}', [SupplierController::class, 'update'])->name('supplier.update');
    });

    //Route Customer
    Route::middleware(['MenuSecure:Customer', 'SetDynamicTitle'])->group(function () {
        Route::get('/customer', [CustomerController::class, 'index'])->name('customer.index');
        Route::get('/customer/create', [CustomerController::class, 'create'])->name('customer.create');
        Route::get('/customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
        Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
        Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
        Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');

        // Wilayah
        Route::post('/customer/getCity', [CustomerController::class, 'getCity']);
        Route::post('/customer/getDistrict', [CustomerController::class, 'getDistrict']);
        Route::post('/customer/getVillage', [CustomerController::class, 'getVillage']);
        Route::post('/customer/getSubvillage', [CustomerController::class, 'getSubVillage']);
        Route::post('/customer/subvillage', [CustomerController::class, 'subvillage']);
        Route::get('customer-export', [CustomerController::class, 'export']);
    });

    //Route Customer Category
    Route::middleware(['MenuSecure:Customer Category', 'SetDynamicTitle'])->group(function () {
        Route::get('/customer/customer_category', [CustomerCategoryController::class, 'index'])->name('customerCategory.index');
        Route::post('/customer/customer_category/create', [CustomerCategoryController::class, 'create'])->name('customerCategory.create');
        Route::delete('/customer/customer_category/destroy/{id}', [CustomerCategoryController::class, 'destroy'])->name('customerCategory.destroy');
        Route::put('/customer/customer_category/update/{id}', [CustomerCategoryController::class, 'update'])->name('customerCategory.update');
    });

    //Route Membership
    Route::middleware(['MenuSecure:Membership'])->group(function () {
        Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
        Route::get('/membership/create', [MembershipController::class, 'create'])->name('membership.create');
        Route::get('/membership/{id}', [MembershipController::class, 'edit'])->name('membership.edit');
        Route::post('/membership/store', [MembershipController::class, 'store'])->name('membership.store');
        Route::put('/membership/{id}', [MembershipController::class, 'update'])->name('membership.update');
        Route::delete('/membership/{id}', [MembershipController::class, 'destroy'])->name('membership.destroy');
    });

    //Route Produk
    Route::middleware(['MenuSecure:Product', 'SetDynamicTitle'])->group(function () {
        //Product All
        Route::get('/product', [ProductController::class, 'index'])->name('product.index');
        Route::get('/product/add', [ProductController::class, 'add'])->name('product.add');
        Route::post('/product/create', [ProductController::class, 'create'])->name('product.create');
        Route::delete('/product/destroy/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
        Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
        Route::put('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
        Route::get('/product/detail/{id}', [ProductController::class, 'detail'])->name('product.detail');
        Route::get('/product/print-barcode', [ProductController::class, 'printBarcode'])->name('product.print-barcode');
        Route::post('/product/print-price-tag', [ProductController::class, 'cetakPriceTag'])->name('product.price-tag');
        Route::put('/product/update-customer-price/{id}', [ProductController::class, 'updateCustomerPrice'])->name('product.update-customer-price');

        Route::post('/product/{id}/toggle-main-stock', [ProductController::class, 'toggleMainStock'])->name('product.toggle-main-stock');
        Route::post('/getProductBundle', [ProductController::class, 'getProductBundle']);
        Route::put('/products/{product}/store-or-update-item-bundle', [ProductController::class, 'storeOrUpdateItemBundle'])->name('product.store-or-update-item-bundle');

        Route::get('/product/export-excel', [ProductController::class, 'exportExcel'])->name('export.excel');

        Route::get('/product/template/import-product', [ProductController::class, 'templateProduct'])->name('template.product');
        Route::get('/product/template/import-stock', [ProductController::class, 'templateStock'])->name('template.stock');
        Route::get('/product/template/import-price', [ProductController::class, 'templatePrice'])->name('template.price');

        Route::post('/product/import-product', [ProductController::class, 'importProduct'])->name('import.product');
        Route::post('/product/import-stock', [ProductController::class, 'importStock'])->name('import.stock');
        Route::post('/product/import-price', [ProductController::class, 'importPrices'])->name('import.price');
    });

    Route::middleware(['MenuSecure:Product Unit', 'SetDynamicTitle'])->group(function () {
        // Product Unit
        Route::get('/product/product_unit', [ProductUnitController::class, 'index'])->name('productUnit.index');
        Route::post('/product/product_unit/create', [ProductUnitController::class, 'create'])->name('productUnit.create');
        Route::delete('/product/product_unit/destroy/{id}', [ProductUnitController::class, 'destroy'])->name('productUnit.destroy');
        Route::put('/product/product_unit/update/{id}', [ProductUnitController::class, 'update'])->name('productUnit.update');
    });

    Route::middleware(['MenuSecure:Loyalty'])->group(function () {
        //Loyalty Point
        Route::get('/loyalty_point', [LoyaltyPointController::class, 'index'])->name('loyalty_point.index');
        Route::get('/loyalty_point/create', [LoyaltyPointController::class, 'create'])->name('loyalty_point.create');
        Route::get('/loyalty_point/{id}/edit', [LoyaltyPointController::class, 'edit'])->name('loyalty_point.edit');
        Route::post('/loyalty_point/store', [LoyaltyPointController::class, 'store'])->name('loyalty_point.store');
        Route::put('/loyalty_point/{id}', [LoyaltyPointController::class, 'update'])->name('loyalty_point.update');
        Route::delete('/loyalty_point/{id}', [LoyaltyPointController::class, 'destroy'])->name('loyalty_point.destroy');

        Route::get('/search-product-loyalty', [LoyaltyPointController::class, 'searchProductLoyalty'])->name('loyalty_point.searchProductLoyalty');
        //Claim Loyalty Point
        Route::get('/loyalty_point/product', [LoyaltyPointController::class, 'getClaimProduct'])->name('loyalty_point.getClaimProduct');
        Route::get('/loyalty_point/discount', [LoyaltyPointController::class, 'getClaimDiscount'])->name('loyalty_point.getClaimDiscount');
        Route::post('/loyalty-claim-product/creteClaimProduct', [LoyaltyPointController::class, 'creteClaimProduct'])->name('loyalty_point.creteClaimProduct');
        Route::post('/loyalty-claim-product/creteClaimDiscount', [LoyaltyPointController::class, 'creteClaimDiscount'])->name('loyalty_point.creteClaimDiscount');
        Route::get('/loyalty_point/product/{id}', [LoyaltyPointController::class, 'editViewProduct'])->name('loyalty_point.editViewProduct');
        Route::post('/loyalty_point/{id}/product', [LoyaltyPointController::class, 'editProsesProduct'])->name('loyalty_point.editProsesProduct');
        Route::delete('/loyalty_point/{id}/product', [LoyaltyPointController::class, 'destroyClaimProduct'])->name('loyalty_point.destroyClaimProduct');
        Route::get('/loyalty_point/discount/{id}', [LoyaltyPointController::class, 'editViewDiscount'])->name('loyalty_point.editViewDiscount');
        Route::post('/loyalty_point/{id}/discount', [LoyaltyPointController::class, 'editProsesDiscount'])->name('loyalty_point.editProsesDiscount');
        Route::delete('/loyalty_point/{id}/discount', [LoyaltyPointController::class, 'destroyClaimDiscount'])->name('loyalty_point.destroyClaimDiscount');

        Route::post('/update-loyalty-status', [LoyaltyPointController::class, 'updateStatus'])->name('loyalty_point.updateStatus');
        Route::put('/update-loyalty', [LoyaltyPointController::class, 'updateLoyalty'])->name('update-loyalty');
    });

    Route::middleware(['MenuSecure:Produk Diskon', 'SetDynamicTitle'])->group(function () {
        //Product Discount
        Route::resource('ProductDiscount', ProductDiscountController::class);
        Route::post('getPriceProduct', [ProductDiscountController::class, 'getPriceProduct']);
    });

    Route::middleware(['MenuSecure:Harga Bertingkat'])->group(function () {
        // Harga Bertingkat
        Route::resource('tiered_prices', TieredPricesController::class);
        Route::delete('/tiered_prices_product/{id}', [TieredPricesController::class, 'product_delete']);
    });

    Route::middleware(['MenuSecure:Mutasi Hadiah'])->group(function () {
        // Mutasi Hadiah
        Route::get('/stock_mutation_reward', [StockMutationRewardController::class, 'index'])->name('stock_mutation_reward.index');
        Route::post('/stock_mutation_reward', [StockMutationRewardController::class, 'store']);
        Route::get('/stock_mutation_reward/create', [StockMutationRewardController::class, 'create']);
        Route::post('/getProduct_reward', [StockMutationRewardController::class, 'getProduct_reward']);
        Route::post('/get_product_reward', [StockMutationRewardController::class, 'get_product_reward']);
        Route::get('/stock_mutation_reward/{id}', [StockMutationRewardController::class, 'show']);
    });

    Route::middleware(['MenuSecure:Brand', 'SetDynamicTitle'])->group(function () {
        // Brand
        Route::get('/brand', [BrandController::class, 'index'])->name('brand.index');
        Route::post('/brand', [BrandController::class, 'store']);
        Route::put('/brand/{id}', [BrandController::class, 'update']);
        Route::delete('/brand/{id}', [BrandController::class, 'destroy']);
    });

    Route::middleware(['MenuSecure:Product Category', 'SetDynamicTitle'])->group(function () {
        // Product Category
        Route::post('/getSubCategories', [ProductCategoryController::class, 'getSubCategories'])->name('productCategory.getSub');
        Route::get('/product/product_category', [ProductCategoryController::class, 'index'])->name('productCategory.index');
        Route::post('/product/product_category/create', [ProductCategoryController::class, 'create'])->name('productCategory.create');
        Route::delete('/product/product_category/destroy/{id}', [ProductCategoryController::class, 'destroy'])->name('productCategory.destroy');
        Route::put('/product/product_category/update/{id}', [ProductCategoryController::class, 'update'])->name('productCategory.update');

        Route::post('/productCategory/store-child', [ProductCategoryController::class, 'storeChild'])->name('productCategory-sub.store');
        Route::put('/productCategory/update-child', [ProductCategoryController::class, 'updateChild'])->name('productCategory.update-child');
        Route::delete('/productCategory/delete-child', [ProductCategoryController::class, 'deleteChild'])->name('productCategory.delete-child');
    });

    Route::middleware(['MenuSecure:Product Price'])->group(function () {
        // Product Price
        Route::get('/product/product_price', [ProductPriceController::class, 'index'])->name('productPrice.index');
        Route::post('/product/product_price/create', [ProductPriceController::class, 'create'])->name('productPrice.create');
        Route::delete('/product/product_price/destroy/{id}', [ProductPriceController::class, 'destroy'])->name('productPrice.destroy');
        Route::put('/product/product_price/update/{id}', [ProductPriceController::class, 'update'])->name('productPrice.update');
    });

    Route::middleware(['MenuSecure:Product Stock'])->group(function () {
        // Product Stock
        Route::get('/product/product_stock', [ProductStockController::class, 'index'])->name('productStock.index');
        Route::post('/product/product_stock/create', [ProductStockController::class, 'create'])->name('productStock.create');
        Route::delete('/product/product_stock/destroy/{id}', [ProductStockController::class, 'destroy'])->name('productStock.destroy');
        Route::put('/product/product_stock/update/{id}', [ProductStockController::class, 'update'])->name('productStock.update');
        Route::get('/product/product_stock/edit', [ProductStockController::class, 'editAllStock'])->name('productStock.editAllStock');
        Route::put('/product/product_stock/update-all-stock', [ProductStockController::class, 'updateAllStock'])->name('productStock.updateAllStock');
    });

    Route::middleware(['MenuSecure:Product Selling Price', 'SetDynamicTitle'])->group(function () {
        // Product Selling Price
        Route::get('/product/product_selling_price', [ProductSellingPriceController::class, 'index'])->name('productSellingPrice.index');
        Route::post('/product/product_selling_price/create', [ProductSellingPriceController::class, 'create'])->name('productSellingPrice.create');
        Route::delete('/product/product_selling_price/destroy/{id}', [ProductSellingPriceController::class, 'destroy'])->name('productSellingPrice.destroy');
        Route::put('/product/product_selling_price/update/{id}', [ProductSellingPriceController::class, 'update'])->name('productSellingPrice.update');
    });

    Route::middleware(['MenuSecure:Inventory', 'SetDynamicTitle'])->group(function () {
        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');;
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{id}/update', [InventoryController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{id}/destroy', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    });

    Route::middleware(['MenuSecure:Stock History', 'SetDynamicTitle'])->group(function () {
        // Product Stock History
        Route::get('/stock_history', [ProductStockHistoryController::class, 'index'])->name('historyStock.index');
    });

    Route::middleware(['MenuSecure:Stock Opname', 'SetDynamicTitle'])->group(function () {
        // Product Stock Opname
        Route::get('/stock_opname', [StockOpnameController::class, 'index'])->name('stockOpname.index');
        Route::get('/stock_opname2', [StockOpnameController::class, 'index2'])->name('stockOpname.index2');
        Route::get('/stock_opname/add', [StockOpnameController::class, 'add'])->name('stockOpname.add');
        Route::get('/stock_opname/add2', [StockOpnameController::class, 'add2'])->name('stockOpname.add2');
        Route::post('/stock_opname/create', [StockOpnameController::class, 'create'])->name('stockOpname.create');
        Route::post('/stock_opname_check_qty', [StockOpnameController::class, 'check_qty']);
        Route::get('/stock_opname/edit/{id}', [StockOpnameController::class, 'edit'])->name('stockOpname.edit');
        Route::put('/stock_opname/update/{id}', [StockOpnameController::class, 'update'])->name('stockOpname.update');
        Route::post('/stock_opname/finish', [StockOpnameController::class, 'finish'])->name('stockOpname.finish');
        Route::get('stock_opname/detail/{id}', [StockOpnameController::class, 'detail'])->name('stockOpname.detail');
        Route::delete('/stock_opname/destroy/{id}', [StockOpnameController::class, 'destroy'])->name('stockOpname.destroy');

        Route::get('/stock_opname_preview/{id}', [StockOpnameController::class, 'preview'])->name('stockOpname.preview');
        Route::get('/stock-opname-data/{code}', [StockOpnameController::class, 'getSoData']);

        Route::post('/getProductOpname', [StockOpnameController::class, 'getProductOpname']);
    });

    Route::middleware(['MenuSecure:Stock Adjustment', 'SetDynamicTitle'])->group(function () {
        // Product Stock Adjustment
        Route::get('/stock_adjustment', [StockAdjustmentController::class, 'index'])->name('stockAdjustment.index');
        Route::post('/stock_adjustment/adjustment', [StockAdjustmentController::class, 'adjustment'])->name('stockAdjustment.adjustment');
        Route::post('/stock_adjustment/store', [StockAdjustmentController::class, 'store'])->name('stockAdjustment.store');
        Route::get('/stock_adjustment/detail/{id}', [StockAdjustmentController::class, 'detail'])->name('stockAdjustment.detail');

        Route::get('/stock_adjustment_preview/{id}', [StockAdjustmentController::class, 'preview'])->name('stockAdjustment.preview');
        Route::get('/stock-adjustment-data/{code}', [StockAdjustmentController::class, 'getDataAdjustment']);
    });

    Route::middleware(['MenuSecure:Stock Mutation', 'SetDynamicTitle'])->group(function () {
        // Stock Mutation
        Route::prefix('/stock_mutation')->name('stockMutation.')->group(function () {
            Route::get('/', [StockMutationController::class, 'index'])->name('index');
            Route::get('/create', [StockMutationController::class, 'create'])->name('create');
            Route::post('/store', [StockMutationController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [StockMutationController::class, 'edit'])->name('edit');
            Route::get('/getProducts', [StockMutationController::class, 'getProducts'])->name('getProducts');
            Route::get('/show/{id}', [StockMutationController::class, 'show'])->name('show');
            Route::get('/print/{id}', [StockMutationController::class, 'print'])->name('print');
            Route::get('/receipt/{id}', [StockMutationController::class, 'receipt'])->name('receipt');
            Route::put('/receive/{id}', [StockMutationController::class, 'receive'])->name('receive');
            Route::put('/update/{id}', [StockMutationController::class, 'update'])->name('update');
            Route::put('/update-status/{id}', [StockMutationController::class, 'updateStatus'])->name('updateStatus');
        });
    });

    Route::middleware(['MenuSecure:Stock Mutation Inventory to Outlet', 'SetDynamicTitle'])->group(function () {
        // Stock Mutation Inventory to Outlet
        Route::prefix('/stock_mutation_inventory_to_outlet')->name('stockMutationInventoryToOutlet.')->group(function () {
            Route::get('/', [StockMutationInventoryOutletController::class, 'index'])->name('index');
            Route::get('/create', [StockMutationInventoryOutletController::class, 'create'])->name('create');
            Route::post('/store', [StockMutationInventoryOutletController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [StockMutationInventoryOutletController::class, 'edit'])->name('edit');
            Route::get('/getProducts', [StockMutationInventoryOutletController::class, 'getProducts'])->name('getProducts');
            Route::get('/getProductsPo', [StockMutationInventoryOutletController::class, 'getProductsPo'])->name('getProductsPo');
            Route::get('/show/{id}', [StockMutationInventoryOutletController::class, 'show'])->name('show');
            Route::get('/print/{id}', [StockMutationInventoryOutletController::class, 'print'])->name('print');
            Route::get('/receipt/{id}', [StockMutationInventoryOutletController::class, 'receipt'])->name('receipt');
            Route::put('/receive/{id}', [StockMutationInventoryOutletController::class, 'receive'])->name('receive');
            Route::put('/update/{id}', [StockMutationInventoryOutletController::class, 'update'])->name('update');
            Route::put('/update-status/{id}', [StockMutationInventoryOutletController::class, 'updateStatus'])->name('updateStatus');
        });

        // NOTE: update stack v2
        Route::prefix('/stock_mutation')->name('stock_mutation.')->group(function () {
            Route::get('/v2', [StockMutationInventoryOutletController::class, 'indexV2'])->name('index-v2');
        });
    });

    Route::middleware(['MenuSecure:Profile Company', 'SetDynamicTitle'])->group(function () {
        // Route Profile Company
        Route::get('/profile_company', [ProfileCompanyController::class, 'index'])->name('profileCompany.index');
        Route::get('/profile_company/create', [ProfileCompanyController::class, 'create'])->name('profileCompany.create');
        Route::post('/profile_company/store', [ProfileCompanyController::class, 'store'])->name('profileCompany.store');
        Route::get('/profile_company/edit/{id}', [ProfileCompanyController::class, 'edit'])->name('profileCompany.edit');
        Route::put('/profile_company/update/{id}', [ProfileCompanyController::class, 'update'])->name('profileCompany.update');
        Route::delete('/profile_company/destroy/{id}', [ProfileCompanyController::class, 'destroy'])->name('profileCompany.destroy');
    });

    Route::middleware(['MenuSecure:Outlet', 'SetDynamicTitle'])->group(function () {
        // Route Outlet
        Route::get('/outlet', [OutletController::class, 'index'])->name('outlet.index');
        Route::get('/outlet/create', [OutletController::class, 'create'])->name('outlet.create');
        Route::post('/outlet', [OutletController::class, 'store'])->name('outlet.store');
        Route::get('/outlet/setting', [OutletController::class, 'setting'])->name('outlet.setting');
        Route::get('/outlet/edit/{id}', [OutletController::class, 'edit'])->name('outlet.edit');
        Route::put('/outlet/update/{id}', [OutletController::class, 'update'])->name('outlet.update');
        Route::delete('/outlet/{id}', [OutletController::class, 'destroy'])->name('outlet.destroy');
        Route::get('/outlet/{id}', [OutletController::class, 'show'])->name('outlet.show');

        Route::get('/outlet/{outlet_id}/table', [OutletTableController::class, 'index'])->name('outlet-table.index');
        Route::post('/outlet/table/create', [OutletTableController::class, 'create'])->name('outlet-table.create');
        Route::delete('/outlet/table/destroy/{id}', [OutletTableController::class, 'destroy'])->name('outlet-table.destroy');
        Route::put('/outlet/table/update/{id}', [OutletTableController::class, 'update'])->name('outlet-table.update');
    });

    Route::middleware(['MenuSecure:Cash Proof In', 'SetDynamicTitle'])->group(function () {
        // Route Cash Proof in
        Route::get('/cash_proof_in', [CashProofInController::class, 'index'])->name('cashProofIn.index');
        Route::get('/cash_proof_in/create', [CashProofInController::class, 'create'])->name('cashProofIn.create');
        Route::post('/cash_proof_in/store', [CashProofInController::class, 'store'])->name('cashProofIn.store');
        Route::get('/cash_proof_in/print/{id}', [CashProofInController::class, 'print'])->name('cashProofIn.print');
        Route::get('/cash_proof_in/receipt/{id}', [CashProofInController::class, 'receipt'])->name('cashProofIn.receipt');
    });

    Route::middleware(['MenuSecure:Cash Proof Out', 'SetDynamicTitle'])->group(function () {
        // Route Cash Proof out
        Route::get('/cash_proof_out', [CashProofOutController::class, 'index'])->name('cashProofOut.index');
        Route::get('/cash_proof_out/create', [CashProofOutController::class, 'create'])->name('cashProofOut.create');
        Route::post('/cash_proof_out/store', [CashProofOutController::class, 'store'])->name('cashProofOut.store');
        Route::get('/cash_proof_out/print/{id}', [CashProofOutController::class, 'print'])->name('cashProofOut.print');
        Route::get('/cash_proof_out/receipt/{id}', [CashProofOutController::class, 'receipt'])->name('cashProofOut.receipt');
    });

    Route::middleware(['MenuSecure:Purchase Requisition', 'SetDynamicTitle'])->group(function () {
        Route::resource('/purchase_requisition', PurchaseRequisitionController::class);
        Route::post('/getProduct_PR', [PurchaseRequisitionController::class, 'getProduct_PR']);
        Route::post('/getProduct_update', [PurchaseRequisitionController::class, 'getProduct_update']);
        Route::get('/purchase_requisition_print/{id}', [PurchaseRequisitionController::class, 'print']);
        Route::get('/purchase_requisition_nota/{code}', [PurchaseRequisitionController::class, 'nota']);
        Route::get('/edit-price-purchase/{id}', [PurchaseRequisitionController::class, 'editPricePurchase']);
        Route::put('/update-price-purchase/{id}', [PurchaseRequisitionController::class, 'updatePricePurchase'])->name('updatePricePurchase.update');
        Route::post('/getDetailProductPR', [PurchaseRequisitionController::class, 'getDetailProductPR']);
        Route::post('/getDataSupplier', [PurchaseRequisitionController::class, 'getDataSupplier']);
    });

    Route::middleware(['MenuSecure:Purchase Order', 'SetDynamicTitle'])->group(function () {
        //Route Purchase Order
        Route::resource('/purchase_order', PurchaseOrderController::class);
        Route::post('/getProduct_po', [PurchaseOrderController::class, 'getProduct_po']);
        Route::post('/getProduct_po_update', [PurchaseOrderController::class, 'getProduct_update']);
        Route::post('/purchase_po_finish', [PurchaseOrderController::class, 'finish']);
        Route::post('purchase_order_void', [PurchaseOrderController::class, 'void']);
        Route::post('/reset_po/{id}', [PurchaseOrderController::class, 'reset']);
        Route::get('/purchase_order_print/{id}', [PurchaseOrderController::class, 'print']);
        Route::get('/purchase_order_nota/{code}', [PurchaseOrderController::class, 'nota']);
        Route::post('/getDataSupplier', [PurchaseOrderController::class, 'getDataSupplier']);
        Route::get('/getDetailProductPO', [PurchaseOrderController::class, 'getDetailProductPO']);
        Route::get('/purchase_order_nota/{code}', [PurchaseOrderController::class, 'nota']);
        Route::post('/purchase_order/{id}/cancel', [PurchaseOrderController::class, 'cancelOrder'])->name('purchase_order.cancel');
    });

    Route::middleware(['MenuSecure:Purchase Reception', 'SetDynamicTitle'])->group(function () {
        //Route Purchase Reception
        Route::resource('/purchase_reception', PurchaseReceptionController::class);
        Route::put('/edit-price-multiple', [PurchaseReceptionController::class, 'editMultiplePrices']);
        Route::post('getPO', [PurchaseReceptionController::class, 'getPO']);
        Route::post('purchase_pn_open', [PurchaseReceptionController::class, 'Open']);
        Route::post('/reception', [PurchaseReceptionController::class, 'reception']);
        Route::post('purchase_reception_void', [PurchaseReceptionController::class, 'void']);
        Route::get('/purchase_reception_print/{id}', [PurchaseReceptionController::class, 'print']);
        Route::get('/purchase_reception_summary/{code}', [PurchaseReceptionController::class, 'summary']);
        Route::get('/purchase_reception_detail_nota/{code}', [PurchaseReceptionController::class, 'detailNota']);
        Route::post('/getDetailProductPN', [PurchaseReceptionController::class, 'getDetailProductPN']);
        Route::post('purchase_pn_finish', [PurchaseReceptionController::class, 'Finish']);
        Route::post('/get_product_bonus', [PurchaseReceptionController::class, 'getProductBonus']);
        Route::post('/get_supplier', [PurchaseReceptionController::class, 'getSupplier']);
        Route::post('/get_product_receiption', [PurchaseReceptionController::class, 'getProductReceiption']);
        Route::post('/purchase_reception/{invoice}/payment', [PurchaseReceptionController::class, 'payment'])->name('purchaseReception.payment');
    });

    Route::middleware(['MenuSecure:Purchase Invoice', 'SetDynamicTitle'])->group(function () {
        // Route Purchase Invoice
        Route::get('/purchase_invoice', [PurchaseInvoiceController::class, 'index'])->name('purchaseInvoice.index');
        Route::get('/purchase_invoice/create', [PurchaseInvoiceController::class, 'create'])->name('purchaseInvoice.create');
        Route::post('/purchase_invoice/create', [PurchaseInvoiceController::class, 'store'])->name('purchaseInvoice.store');
        Route::get('/purchase_invoice/export', [PurchaseInvoiceController::class, 'export']);
        Route::get('/purchase_invoice/{id}', [PurchaseInvoiceController::class, 'getInvoiceDetails'])->name('purchaseInvoice.details');

        Route::get('/purchase_invoice_show/{id}', [PurchaseInvoiceController::class, 'show'])->name('purchaseInvoice.show');
        Route::get('/purchase_invoice_nota/{code}', [PurchaseInvoiceController::class, 'nota']);
    });

    Route::middleware(['MenuSecure:Invoice Payment', 'SetDynamicTitle'])->group(function () {
        // Route Invoice Payment
        Route::get('/invoice_payment', [InvoicePaymentController::class, 'index'])->name('invoicePayment.index');
        Route::get('/invoice_payment/{id}', [InvoicePaymentController::class, 'create'])->name('invoicePayment.create');
        Route::post('/invoice_payment/store', [InvoicePaymentController::class, 'store'])->name('invoicePayment.store');
    });

    Route::middleware(['MenuSecure:Purchase Return', 'SetDynamicTitle'])->group(function () {
        // Route Purchase Return
        Route::resource('/purchase_return', PurchaseReturnController::class);
        Route::post('/purchase_return_getdatapo', [PurchaseReturnController::class, 'getDataPO']);
        Route::post('/purchase_return_getdataretur', [PurchaseReturnController::class, 'getDataRetur']);
        Route::post('/purchase_return_finish', [PurchaseReturnController::class, 'finish']);
        Route::post('/getDetailProductRT', [PurchaseReturnController::class, 'getDetailProductRT']);
        Route::get('/purchase_return_print/{id}', [PurchaseReturnController::class, 'print']);
        Route::get('/purchase_return_nota/{code}', [PurchaseReturnController::class, 'nota']);
    });

    Route::middleware(['MenuSecure:Sales', 'SetDynamicTitle'])->group(function () {
        //Route Sales
        Route::get('/sales/export/{type}', [SalesController::class, 'export'])->name('sales.export');
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/{code}', [SalesController::class, 'show'])->name('sales.show');
        Route::post('/sales/create', [SalesController::class, 'create'])->name('sales.create');
        Route::delete('/sales/destroy/{sales}', [SalesController::class, 'destroy'])->name('sales.destroy');
        Route::put('/sales/update/{sales}', [PaymentMethodController::class, 'update'])->name('sales.update');
        Route::get('/sales-nota/{code}/{type}', [SalesController::class, 'getNota']);
        Route::get('/sales/{sale}/detail', [SalesController::class, 'detail']);
        Route::get('/sales/{sale}/print-logs', [SalesController::class, 'printLogs']);

        Route::post('/update-shipping-status', [SalesController::class, 'updateShippingStatus'])->name('update.shipping.status');

        Route::put('/sales/{id}/update-due-date', [DebtPaymentController::class, 'updateDueDate'])->name('sales.updateDueDate');

        //Route Discount Global
        Route::get('/discount', [DiscountController::class, 'index'])->name('discount.index');
        Route::get('/discount/create', [DiscountController::class, 'create'])->name('discount.create');
        Route::post('/discount/store', [DiscountController::class, 'store'])->name('discount.store');
        Route::get('/discount/{id}/edit', [DiscountController::class, 'edit'])->name('discount.edit');
        Route::put('/discount/{id}', [DiscountController::class, 'update'])->name('discount.update');
        Route::delete('/discount/{id}', [DiscountController::class, 'destroy'])->name('discount.destroy');
    });

    Route::middleware(['MenuSecure:Payment Method', 'SetDynamicTitle'])->group(function () {
        //Route Payment Method
        Route::get('/payment_method', [PaymentMethodController::class, 'index'])->name('paymentMethod.index');
        // Route::post('/payment_method/create', [PaymentMethodController::class, 'create'])->name('paymentMethod.create');
        // Route::delete('/payment_method/destroy/{id}', [PaymentMethodController::class, 'destroy'])->name('paymentMethod.destroy');
        // Route::put('/payment_method/update/{id}', [PaymentMethodController::class, 'update'])->name('paymentMethod.update');
        Route::patch('/payment-methods/{id}/toggle', [PaymentMethodController::class, 'toggleStatus'])->name('payment-method.toggle');
    });

    Route::middleware(['MenuSecure:Cashflow Close', 'SetDynamicTitle'])->group(function () {
        // Route Cashflow Close
        Route::get('/cashflow_close', [CashflowCloseController::class, 'index'])->name('cashflowClose.index');
        Route::get('/cashflow_close/print/{id}', [CashflowCloseController::class, 'print'])->name('cashflowClose.print');
        Route::get('/cashflow_close/receipt/{code}', [CashflowCloseController::class, 'receipt'])->name('cashflowClose.receipt');

        Route::get('/working-hours', App\Http\Livewire\WorkingHours::class)->name('working-hours');
    });

    Route::middleware(['MenuSecure:Retur Sales', 'SetDynamicTitle'])->group(function () {
        //route retur sales
        Route::resource('/retur-sales', ReturSalesController::class);
        Route::post('retur-sales/getData', [ReturSalesController::class, 'getData']);
        Route::post('retur-sales/getDetailSales', [ReturSalesController::class, 'getDetailSales']);
        Route::post('retur-sales/finish', [ReturSalesController::class, 'finish']);
        Route::get('retur-sales/{id}/detail', [ReturSalesController::class, 'detail']);
        Route::get('retur-sales/{code}', [ReturSalesController::class, 'show']);
        Route::get('retur-sales/receipt/{code}', [ReturSalesController::class, 'receipt']);
    });

    Route::middleware(['MenuSecure:Debt Payment', 'SetDynamicTitle'])->group(function () {
        //route debt payment
        Route::get('/debt_payment', [DebtPaymentController::class, 'index'])->name('debtPayment.index');
        Route::get('/debt_payment/{id}', [DebtPaymentController::class, 'create'])->name('debtPayment.create');
        Route::post('/debt_payment/store', [DebtPaymentController::class, 'store'])->name('debtPayment.store');
        Route::get('/debt_payment/{id}/print', [DebtPaymentController::class, 'print'])->name('debtPayment.print');
        // Route::get('/debt_payment/{id}/detail', [DebtPaymentController::class, 'detail'])->name('debtPayment.detail');
        // Route::get('/debt_payment/{id}/receipt', [DebtPaymentController::class, 'receipt'])->name('debtPayment.receipt');
        // Route::get('/purchase_invoice_nota/{code}', [PurchaseInvoiceController::class, 'nota']);
        // nota
        Route::get('/debt_payment/{id}/nota', [DebtPaymentController::class, 'nota'])->name('debtPayment.nota');
        Route::get('/debt_payment/{id}/nota58', [DebtPaymentController::class, 'nota58'])->name('debtPayment.nota58');
        Route::get('/debt_payment/{id}/nota80', [DebtPaymentController::class, 'nota80'])->name('debtPayment.nota80');
    });

    Route::middleware(['MenuSecure:Cashier Machine'])->group(function () {
        // Route  Cashier Machine
        Route::resource('cashier_machine', CashierMachineController::class);
    });

    // Route Role & Permission
    Route::middleware(['MenuSecure:Role', 'SetDynamicTitle'])->group(function () {
        // Route Role
        Route::get('/role', [RoleController::class, 'index'])->name('role.index');
        Route::post('/role/create', [RoleController::class, 'create'])->name('role.create');
        Route::put('/role/update/{id}', [RoleController::class, 'update'])->name('role.update');
        Route::delete('/role/destroy/{id}', [RoleController::class, 'destroy'])->name('role.destroy');
    });

    Route::middleware(['MenuSecure:Permission', 'SetDynamicTitle'])->group(function () {
        // Route Permission
        Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
        Route::get('/permission/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('/permission', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('/permission/edit/{id}', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::put('/permission/update/{id}', [PermissionController::class, 'update'])->name('permission.update');

        // Route Menu
        Route::get('/menu/create', [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
    });

    // Route Report
    Route::prefix('report')->group(function () {
        Route::middleware(['MenuSecure:Report Void Penjualan', 'SetDynamicTitle'])->group(function () {
            Route::get('/sales/void', [SalesReportController::class, 'reportVoid']);
            Route::get('/sales/void-report-print', [SalesReportController::class, 'reportVoidPrint'])->name('reportVoidPrint');
        });

        Route::middleware(['MenuSecure:Report Laba Rugi', 'SetDynamicTitle'])->group(function () {
            Route::get('/laba-rugi', [ReportLabaRugiController::class, 'index']);
            Route::get('/laba-rugi/print', [ReportLabaRugiController::class, 'print']);
        });

        Route::middleware(['MenuSecure:Report Sales', 'SetDynamicTitle'])->group(function () {
            Route::get('/sales', [SalesReportController::class, 'reportSales']);
            Route::get('/sales-data', [SalesReportController::class, 'dataSales']);
            Route::get('/generate-excel', [SalesReportController::class, 'generateExcel'])->name('generate.excel');

            Route::get('/cashier/by-outlet', [SalesReportController::class, 'getCashierByOutlet'])->name('cashier.byOutlet');
            Route::get('/product/by-outlet', [SalesReportController::class, 'getProductByOutlet'])->name('product.byOutlet');
        });

        Route::middleware(['MenuSecure:Report Sales Overview', 'SetDynamicTitle'])->group(function () {
            Route::get('/sales-overview', [SalesReportController::class, 'overviewSales']);
        });

        Route::middleware(['MenuSecure:Report Sales Return'])->group(function () {
            Route::get('/sales/return', [SalesReturnReportController::class, 'index']);
            Route::get('/sales/return/print', [SalesReturnReportController::class, 'print']);
        });

        Route::middleware(['MenuSecure:Report Purchase Order', 'SetDynamicTitle'])->group(function () {
            Route::get('/purchase/purchase_order', [PurchaseOrderReportController::class, 'index']);
            Route::get('/purchase/purchase_order/print', [PurchaseOrderReportController::class, 'print']);
        });

        Route::middleware(['MenuSecure:Report Purchase Reception', 'SetDynamicTitle'])->group(function () {
            Route::get('/purchase/purchase_reception', [PurchaseReceptionReportController::class, 'index']);
            Route::get('/purchase/purchase_reception/print', [PurchaseReceptionReportController::class, 'print']);
        });

        Route::middleware(['MenuSecure:Report Purchase Return', 'SetDynamicTitle'])->group(function () {
            Route::get('/purchase/purchase_return', [PurchaseReturnReportController::class, 'index']);
            Route::get('/purchase/purchase_return/print', [PurchaseReturnReportController::class, 'print']);
        });

        Route::middleware(['MenuSecure:Report Inventory', 'SetDynamicTitle'])->group(function () {
            Route::get('/inventory/stock_mutation', [StockMutationReportController::class, 'index']);
            Route::get('/inventory/stock_mutation/print', [StockMutationReportController::class, 'print']);

            Route::get('/inventory/stock_opname', [StockOpnameReportController::class, 'index']);
            Route::get('/inventory/stock_opname/print', [StockOpnameReportController::class, 'print']);

            Route::get('/inventory/stock_adjustment', [StockAdjustmentReportController::class, 'index']);
            Route::get('/inventory/stock_adjustment/print', [StockAdjustmentReportController::class, 'print']);

            Route::get('/inventory/stock_gudang', [StockValueReportController::class, 'indexStockGudang'])->name('indexStockGudang.index');
            Route::get('/inventory/stock_gudang/print', [StockValueReportController::class, 'printStockGudang']);

            Route::get('/inventory/stock_outlet', [StockOutletReportController::class, 'indexStockOutlet'])->name('indexStockOutlet.index');
            Route::get('/inventory/stock_outlet/print', [StockOutletReportController::class, 'printStockOutlet']);

            Route::get('/inventory/stock_gudang/consolidation', [StockInventoryConsolidationController::class, 'stockGudangConsolidation']);
            Route::get('/inventory/stock_gudang/consolidation-data', [StockInventoryConsolidationController::class, 'stockGudangConsolidationData']);

            Route::get('/inventory/stock_outlet/consolidation', [StockOutletConsolidationController::class, 'stockOutletConsolidation']);
            Route::get('/inventory/stock_outlet/consolidation-data', [StockOutletConsolidationController::class, 'stockOutletConsolidationData']);
        });

        Route::get('/sales/payment', [PaymentReportController::class, 'index']);
        Route::get('/sales/payment/print', [PaymentReportController::class, 'print']);

        Route::get('/debt/debt', [DebtReportController::class, 'index']);
        Route::get('/debt/debt/print', [DebtReportController::class, 'print']);

        Route::get('/debt/payment', [DebtPaymentReportController::class, 'index']);
        Route::get('/debt/payment/print', [DebtPaymentReportController::class, 'print']);

        Route::middleware(['SetDynamicTitle'])->group(function () {
            Route::get('/stock-value-report', StockValueReport::class)->name('report.stock-value');
            Route::post('/stock-value-report/export', [StockValueReportController::class, 'export'])->name('report.stock-value.export');
        });
    });

    Route::middleware(['MenuSecure:Accounting'])->group(function () {
        //Accounting group
        Route::prefix('accounting')->group(function () {
            Route::get('/journal_stock_account', [JournalStockAccountController::class, 'index']);
            Route::get('/journal_stock_account/create', [JournalStockAccountController::class, 'create']);
            Route::post('/journal_stock_account', [JournalStockAccountController::class, 'store']);
            Route::get('/journal_stock_account/{id}/edit', [JournalStockAccountController::class, 'edit']);
            Route::put('/journal_stock_account/update', [JournalStockAccountController::class, 'update']);

            //journal account
            Route::resource('/journal_account', JournalAccountController::class);
            Route::resource('/journal_account_type', JournalAccountTypeController::class);
            Route::resource('/journal_type', JournalTypeController::class);

            //journal transaction
            Route::resource('/journal_transaction', JournalTransactionController::class);
            Route::post('/journal_transaction/print', [JournalTransactionController::class, 'journalTransactionPrint'])->name('journalTransaction.print');
            Route::get('/journal-transaction-data', [JournalTransactionController::class, 'previewData']);

            Route::resource('/journal_closing', JournalClosingController::class);
            Route::resource('/ledger', LedgerController::class);

            Route::get('/stock/value-report', [StockController::class, 'valueReport']);
            Route::get('/stock/value-report/print', [StockController::class, 'valueReportPrint']);

            Route::get('/journal_po', [JournalPOController::class, 'index']);
            Route::get('/journal_po/{id}', [JournalPOController::class, 'create']);
            Route::post('/journal_po', [JournalPOController::class, 'store']);

            Route::get('/journal_retur_purchase', [JournalReturPurchaseController::class, 'index']);
            Route::get('/journal_retur_purchase/{id}', [JournalReturPurchaseController::class, 'create']);
            Route::post('/journal_retur_purchase/retur', [JournalReturPurchaseController::class, 'retur']);
            Route::post('/journal_retur_purchase', [JournalReturPurchaseController::class, 'store']);

            Route::get('/journal_adjustment', [JournalAdjustmentController::class, 'index']);
            Route::get('/journal_adjustment/{id}', [JournalAdjustmentController::class, 'create']);
            Route::post('/journal_adjustment', [JournalAdjustmentController::class, 'store']);
            Route::delete('/journal_adjustment/{id}', [JournalAdjustmentController::class, 'destroy']);
            Route::post('/journal_adjustment/finish', [JournalAdjustmentController::class, 'finish']);
            Route::resource('/cash_master', CashMasterController::class);

            Route::get('/balance', [BalanceController::class, 'index']);
            Route::get('/balance/print', [BalanceController::class, 'print']);

            Route::get('/profit_loss', [ProfitLossController::class, 'index']);
            Route::get('/profit-loss-data', [ProfitLossController::class, 'previewProfitLossData']);

            Route::get('/journal_retur_sales', [JournalReturSalesController::class, 'index']);
            Route::get('/journal_retur_sales/{id}', [JournalReturSalesController::class, 'create']);
            Route::post('/journal_retur_sales', [JournalReturSalesController::class, 'store']);
            Route::post('/journal_retur_sales/finish', [JournalReturSalesController::class, 'finish']);
        });
    });

    Route::middleware(['MenuSecure:User', 'SetDynamicTitle'])->group(function () {
        //Route Users

        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/users/store', [UsersController::class, 'store'])->name('users.store');
        Route::get('/users/edit/{id}', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('/users/update/{id}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('/users/destroy/{id}', [UsersController::class, 'destroy'])->name('users.destroy');

        // User Inventories
        Route::get('/users/{user_id}/inventory', [UsersController::class, 'getUserInventories'])->name('users.inventory');
        Route::put('/users/{user_id}/inventory', [UsersController::class, 'updateUserInventories'])->name('users.inventory.update');

        // User Outlet
        Route::get('/users/{user_id}/outlet', [UsersController::class, 'getUserOutlet'])->name('users.outlet');
        Route::put('/users/{user_id}/outlet', [UsersController::class, 'updateUserOutlet'])->name('users.outlet.update');
    });

    Route::middleware(['MenuSecure:Setting Product Stock', 'SetDynamicTitle'])->group(function () {
        // Route Settings / Pengaturan
        Route::get('/settings/product_stock', [SettingProductStock::class, 'index'])->name('settingProductStock.index');
        Route::post('/settings/product_stock/update', [SettingProductStock::class, 'updateAllStok'])->name('settingProductStock.updateAllStok');
    });

    Route::middleware(['MenuSecure:Setting Stock Reminder', 'SetDynamicTitle'])->group(function () {
        Route::get('/settings/pos', [SettingsController::class, 'index'])->name('setting.pos.index');
        Route::get('/settings/report', SettingReport::class)->name('setting.report');

        // Route::post('/settings/stock_alert', [SettingsController::class, 'stock_alert'])->name('setting.pos.stock_alert');
        // Route::post('/settings/stock_minus', [SettingsController::class, 'stock_minus'])->name('setting.pos.stock_minus');
        // Route::post('/settings/superior_validation', [SettingsController::class, 'superior_validation'])->name('setting.pos.superior_validation');
        // Route::post('/settings/minus_price', [SettingsController::class, 'minus_price'])->name('setting.pos.minus_price');
        // Route::post('/settings/price_change', [SettingsController::class, 'price_change'])->name('setting.pos.price_change');
        // Route::post('/settings/show_recent_sales', [SettingsController::class, 'show_recent_sales'])->name('setting.pos.show_recent_sales');
        // Route::post('/settings/change_qty_direct_after_add', [SettingsController::class, 'change_qty_direct_after_add'])->name('setting.pos.change_qty_direct_after_add');
    });
});

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('post.login');

Route::get('/r/{receipt_code}', [ReceiptController::class, 'receipt']);

Route::middleware(['auth'])->prefix('developer')->name('developer.')->group(function () {
    // Route::get('/', Index::class)->name('index');
    Route::get('/setup', Setup::class)->name('setup');
    Route::get('/complete-seed', CompleteSeed::class)->name('complete-seed');
});

Route::get('/cek/{id}', [CashflowController::class, '__getSummaryProductSold']);
