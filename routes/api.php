<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CashflowCloseController;
use App\Http\Controllers\API\CashflowController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\Developer\AdjustFixDataController;
use App\Http\Controllers\API\KitchenController;
use App\Http\Controllers\API\Minimarket\AttributeController as MinimarketAttributeController;
use App\Http\Controllers\API\Minimarket\AuthController as MinimarketAuthController;
use App\Http\Controllers\API\Minimarket\CashflowController as MinimarketCashflowController;
use App\Http\Controllers\API\Minimarket\CustomerController as MinimarketCustomerController;
use App\Http\Controllers\API\Minimarket\DashboardController as MinimarketDashboardController;
use App\Http\Controllers\API\Minimarket\ProductController as MinimarketProductController;
use App\Http\Controllers\API\Minimarket\SettingController as MinimarketSettingController;
use App\Http\Controllers\API\Minimarket\StatusController as MinimarketStatusController;
// FOR MINIMARKET
use App\Http\Controllers\API\Minimarket\TransactionController as MinimarketTransactionController;
use App\Http\Controllers\API\OutletController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\SaleController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\v2\AttributeController as AppAttributeController;
use App\Http\Controllers\API\v2\AuthController as AppAuthController;
use App\Http\Controllers\API\v2\CashflowController as AppCashflowController;
use App\Http\Controllers\API\v2\CustomerController as AppCustomerController;
// FOR DEFAULT APP
use App\Http\Controllers\API\v2\ProductController as AppProductController;
use App\Http\Controllers\API\v2\ProfileController as AppProfileController;
use App\Http\Controllers\API\v2\TransactionController as AppTransactionController;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

// START API UNTUK DEFAULT APP===============================
Route::prefix('v2')->group(function () {
    Route::post('/login', [AppAuthController::class, 'login']);
    Route::post('/forgot-password', [AppAuthController::class, 'forgotPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/login-with-pin', [AppAuthController::class, 'loginWithPin']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::prefix('/products')->group(function () {
            Route::get('/', [AppProductController::class, 'index']);
            Route::put('/{product}/update', [AppProductController::class, 'update']);
            Route::post('/store', [AppProductController::class, 'store']);
            Route::delete('/{product}/delete', [AppProductController::class, 'destroy']);

            Route::get('/selling_price', [AppProductController::class, 'productSellingPrice']);

            Route::get('/categories', [AppProductController::class, 'categories']);
            Route::put('/{category}/update', [AppProductController::class, 'categoryUpdate']);
            Route::post('/category/store', [AppProductController::class, 'categoryStore']);
            Route::delete('/{category}/delete', [AppProductController::class, 'categoryDestroy']);
        });

        Route::prefix('/attributes')->group(function () {
            // customer management
            Route::get('/customers', [AppCustomerController::class, 'customers']);
            Route::post('/customers/create', [AppCustomerController::class, 'customersCreate']);
            Route::put('/customers/update/{id}', [AppCustomerController::class, 'customersUpdate']);
            Route::delete('/customers/delete/{id}', [AppCustomerController::class, 'customersDelete']);

            Route::get('/tables', [AppAttributeController::class, 'tables']);
            Route::get('/outlets', [AppAttributeController::class, 'outlets']);
            Route::put('/outlets/update_footer_note', [AppAttributeController::class, 'outletUpdateFooterNote']);
            Route::get('/statistic', [AppAttributeController::class, 'statistic']);
            Route::get('/discount', [AppAttributeController::class, 'discount']);
        });

        Route::prefix('settings')->group(function () {
            // profile management
            Route::get('/profile', [AppProfileController::class, 'profile']);
            Route::put('/profile/update/{id}', [AppProfileController::class, 'update']);
        });

        Route::prefix('transactions')->group(function () {

            // payment method
            Route::get('/payment-method', [AppTransactionController::class, 'paymentMethod']);

            // cashflow open
            Route::post('/cashflow', [AppCashflowController::class, 'store']);
            Route::get('/cashflow/is_open', [AppCashflowController::class, 'checkIsOpen']);
            // cashflow create in / out
            Route::post('/cashflow/create', [AppCashflowController::class, 'cashflowInOut']);
            // cashflow data
            Route::get('/cashflow/list', [AppCashflowController::class, 'dataCashflow']);
            Route::post('/cashflow/close', [AppCashflowController::class, 'CashflowClose']);
            Route::get('/cashflow/close/history', [AppCashflowController::class, 'CashflowCloseHistory']);
            Route::get('/cashflow/close/history/detail', [AppCashflowController::class, 'CashflowCloseHistoryDetail']);

            Route::get('/recent', [AppTransactionController::class, 'recentTransaction']);
            Route::get('/history', [AppTransactionController::class, 'historyTransaction']);
            Route::get('/detail', [AppTransactionController::class, 'detailTransaction']);

            Route::get('/{sale}/detail', [AppTransactionController::class, 'show']);
            Route::post('/sales', [AppTransactionController::class, 'store']);
            Route::get('/sales/recent', [AppTransactionController::class, 'detailRecentTransaction']);
            Route::delete('/sales/delete', [AppTransactionController::class, 'deleteTransaction']);
            Route::post('/sales/pay', [AppTransactionController::class, 'pay']);
            Route::get('/{sale}/edit', [AppTransactionController::class, 'edit']);
            Route::delete('/{sale}/delete', [AppTransactionController::class, 'destroy']);
        });

        Route::resource('customers', CustomerController::class);
        // Route::resource('employees', EmployeeController::class);

        Route::prefix('account')->group(function () {});

        // Route::resource('payment', PaymentController::class);

        // Route::resource('tax', TaxController::class);

        // Route::resource('discount', DiscountController::class);

        // Route::resource('table', TableController::class);

        // Route::prefix('role_management')->group(function () {
        //     Route::get('/roles', [RoleManagementController::class, 'roles']);
        //     Route::get('/permissions', [RoleManagementController::class, 'permissions']);
        //     Route::get('/users', [RoleManagementController::class, 'users']);
        //     Route::get('/{user}/edit', [RoleManagementController::class, 'edit']);
        //     Route::put('/{user}/update', [RoleManagementController::class, 'update']);
        // });

        // Route::resource('user', UserController::class);
    });
});
// END API UNTUK DEFAULT =================================

// START API UNTUK DEFAULT ===============================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login-with-pin', [AuthController::class, 'loginWithPin']);
Route::middleware('auth:sanctum')->group(function () {
    // auth and user
    Route::post('/logout', [AuthController::class, 'logout']);
    // Sales Get Data
    Route::get('/dashboard', [SaleController::class, 'dashboard']);
    Route::get('/products/{selling_price_id}', [SaleController::class, 'products']);
    Route::get('/categories', [SaleController::class, 'categories']);
    Route::get('/outlets', [SaleController::class, 'outlets']);
    Route::get('/machine', [SaleController::class, 'machine']);
    Route::get('/products/{selling}/category/{category:slug}', [SaleController::class, 'filterByCategory']);
    Route::get('/selling_price', [SaleController::class, 'sellingPrice']);
    Route::get('/payment_methods', [SaleController::class, 'paymentMethod']);
    Route::get('/get_channels', [SaleController::class, 'getChannels']);
    Route::get('/transaction/recent', [TransactionController::class, 'recentTransaction']);
    Route::get('/transaction/{id}/detail', [TransactionController::class, 'show']);

    // Order
    Route::middleware(['MenuSecure:Selling Order'])->group(function () {
        Route::post('/transaction/sales', [TransactionController::class, 'store']);
        Route::get('/transaction/{sales}/edit', [TransactionController::class, 'edit']);
        Route::delete('/transaction/{sales}/delete', [TransactionController::class, 'destroy']);
    });

    // Payment
    Route::middleware(['MenuSecure:Selling Payment'])->group(function () {
        Route::post('/transaction/join', [TransactionController::class, 'joinBill']);
        Route::post('/transaction/pay', [TransactionController::class, 'pay']);
    });

    // Kitchen
    Route::middleware(['MenuSecure:Selling Kitchen'])->group(function () {
        Route::get('/kitchen/orders', [KitchenController::class, 'orderLists']);
        Route::put('/kitchen/orders/update', [KitchenController::class, 'updateDetailStatus']);
    });

    // Customers Controller
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers/store', [CustomerController::class, 'store']);
    Route::get('/cashflow/is_open', [CashflowController::class, 'checkIsOpen']);
    Route::resource('/cashflow', CashflowController::class)->only(['store']);
    Route::resource('/cashflow_close', CashflowCloseController::class)->only(['create', 'store']);
    // settings controller
    Route::get('/settings/stock_reminder', [SettingController::class, 'stock']);
    // products controller
    Route::get('/products/stock_minimum_reminder', [ProductController::class, 'productStockForReminder']);
    // Outlet
    Route::get('/outlets/{outlet_id}/tables', [OutletController::class, 'tables']);
});
// END API UNTUK DEFAULT =================================

// START API UNTUK MINIMARKET ===============================
Route::prefix('minimarket')->group(function () {
    Route::post('/login', [MinimarketAuthController::class, 'login']);
    Route::post('/forgot-password', [MinimarketAuthController::class, 'forgotPassword']);
    Route::post('/login-with-pin', [MinimarketAuthController::class, 'loginWithPin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [MinimarketAuthController::class, 'user']);
        Route::post('/logout', [MinimarketAuthController::class, 'logout']);

        Route::prefix('/products')->group(function () {

            Route::get('/', [MinimarketProductController::class, 'index']);
            Route::get('/tier-prices', [MinimarketProductController::class, 'tierPrices']);
            Route::get('/discounts', [MinimarketProductController::class, 'discounts']);
            Route::get('/barcode/{code}', [MinimarketProductController::class, 'getProductByBarcode']);
            // Route::put('/{product}/update', [ProductController::class, 'update']);
            // Route::post('/store', [ProductController::class, 'store']);
            // Route::delete('/{product}/delete', [ProductController::class, 'destroy']);

            // Route::get('/categories', [ProductController::class, 'categories']);
            // Route::put('/{category}/update', [ProductController::class, 'categoryUpdate']);
            // Route::post('/category/store', [ProductController::class, 'categoryStore']);
            // Route::delete('/{category}/delete', [ProductController::class, 'categoryDestroy']);
        });

        // attribute
        Route::prefix('/attributes')->group(function () {
            Route::get('/banks', [MinimarketAttributeController::class, 'banks']);

            // Route::get('/tables', [AttributeController::class, 'tables']);
            Route::get('/customers', [MinimarketCustomerController::class, 'index']);
            Route::get('/discount', [MinimarketAttributeController::class, 'discount']);
            Route::get('/superiors', [MinimarketAttributeController::class, 'getSuperiors']);
            Route::post('/validate-superior', [MinimarketAttributeController::class, 'validateSuperior']);
            // Route::get('/outlets', [AttributeController::class, 'outlets']);
            // Route::get('/statistic', [AttributeController::class, 'statistic']);
        });

        // cashflow
        Route::prefix('/cashflow')->group(function () {
            Route::get('/', [MinimarketCashflowController::class, 'index']);
            Route::get('/is_open', [MinimarketCashflowController::class, 'checkIsOpen']);
            Route::post('/store', [MinimarketCashflowController::class, 'store']);
            Route::post('/close', [MinimarketCashflowController::class, 'close']);
        });

        Route::prefix('transactions')->group(function () {
            // Route::get('/recent', [TransactionController::class, 'recentTransaction']);
            Route::get('/{sale}/detail', [MinimarketTransactionController::class, 'show']);
            Route::post('/store', [MinimarketTransactionController::class, 'store']);
            // Route::get('/{sale}/edit', [TransactionController::class, 'edit']);
            // Route::delete('/{sale}/delete', [TransactionController::class, 'destroy']);
            // getDrafts
            Route::post('/save-as-draft', [MinimarketTransactionController::class, 'saveAsDraft']);
            Route::post('/update-draft', [MinimarketTransactionController::class, 'updateDraft']);
            Route::get('/drafts', [MinimarketTransactionController::class, 'getDrafts']);
            Route::delete('/drafts/{sales_id}', [MinimarketTransactionController::class, 'deleteDraft']);
            Route::post('/void', [MinimarketTransactionController::class, 'void']);
            Route::post('/void-without-validation', [MinimarketTransactionController::class, 'voidWithoutValidation']);
            Route::post('{id}/sync-prices', [MinimarketTransactionController::class, 'syncPrices']);

            Route::get('/histories', [MinimarketTransactionController::class, 'getHistories']);
        });

        Route::prefix('dashboard')->group(function () {
            Route::get('/', [MinimarketDashboardController::class, 'index']);
            Route::get('/chart-data', [MinimarketDashboardController::class, 'chartData']);
        });

        Route::post('/customers', [MinimarketCustomerController::class, 'store']);
        Route::get('/search-address', [MinimarketAttributeController::class, 'searchAddress']);

        // Route::resource('employees', EmployeeController::class);

        Route::prefix('settings')->group(function () {
            Route::get('/', [MinimarketSettingController::class, 'index']);
            Route::get('is-need-validation-when-remove-item', [MinimarketSettingController::class, 'isNeedValidationWhenRemoveItem']);
        });

        Route::get('/payment_methods/fees/{payment_method_name}', [MinimarketAttributeController::class, 'getPaymentMethodFees']);
        Route::get('/payment_methods', [MinimarketAttributeController::class, 'getAllPaymentMethods']);

        // Route::resource('payment', PaymentController::class);

        // Route::resource('tax', TaxController::class);

        // Route::resource('discount', DiscountController::class);

        // Route::resource('table', TableController::class);

        // Route::prefix('role_management')->group(function () {
        //     Route::get('/roles', [RoleManagementController::class, 'roles']);
        //     Route::get('/permissions', [RoleManagementController::class, 'permissions']);
        //     Route::get('/users', [RoleManagementController::class, 'users']);
        //     Route::get('/{user}/edit', [RoleManagementController::class, 'edit']);
        //     Route::put('/{user}/update', [RoleManagementController::class, 'update']);
        // });

        // Route::resource('user', UserController::class);
        Route::get('reprint-log/{sales}', [MinimarketTransactionController::class, 'reprintLog']);

        Route::post('/refund/{sales}/full', [MinimarketTransactionController::class, 'refundFull']);
        Route::post('/refund/{sales}/partial', [MinimarketTransactionController::class, 'refundPartial']);
    });
    Route::get('payment-status-list', [MinimarketStatusController::class, 'paymentStatusList']);
    Route::get('shipping-status-list', [MinimarketStatusController::class, 'shippingStatusList']);
    Route::post('update-shipping-status/{sales}', [MinimarketStatusController::class, 'updateShippingStatusSales']);
});
// END API UNTUK MINIMARKET =================================

Route::prefix('developer')->group(function () {
    Route::post('import-product', [ProductController::class, 'importProduct']);
    Route::post('import-tiered-price', [ProductController::class, 'importTieredPrice']);
    Route::post('import-product-stock', [ProductController::class, 'importStock']);


    Route::put('purchase-detail', [AdjustFixDataController::class, 'wrongPurchaseDetail']);
    Route::put('reset/product-category-code', [AdjustFixDataController::class, 'productCategoryCode']);
});
