<?php

use Carbon\Carbon;
use App\Models\Menu;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Inventory;
use App\Models\Permission;
use App\Models\UserOutlet;
use Illuminate\Support\Str;
use App\Models\ProfilCompany;
use App\Models\UserInventory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;

if (! function_exists('formatDecimal')) {
    function formatDecimal($value)
    {
        if (fmod($value, 1) != 0) {
            return number_format($value, 2, '.', '');
        }

        return (int) $value;
    }
}
if (! function_exists('newRupiah')) {
    function newRupiah($number)
    {
        return 'Rp' . number_format($number, 0, ',', '.');
    }
}

if (! function_exists('decimalToRupiahView')) {
    function decimalToRupiahView($number)
    {
        return "Rp " . number_format($number, ($number == floor($number) ? 0 : 2), ',', '.');
    }
}
if (! function_exists('floatToRupiahView')) {
    function floatToRupiahView($number)
    {
        $number = floatval($number); // Paksa jadi float
        // \Log::info($number);
        // \Log::info(number_format($number, 2, ',', '.'));
        return "Rp " . number_format($number, 2, ',', '.');
    }
}

if (! function_exists('getOutletActive')) {
    function getOutletActive()
    {
        if (Auth::user()) {
            $cariOutletActive = Outlet::where('id', UserOutlet::where('user_id', auth()->user()?->id)->first()?->outlet_id)?->first();
            return $cariOutletActive;
        }
    }
}
if (! function_exists('getMenuPermissions')) {
    function getMenuPermissions()
    {
        $cariOutletActive = getOutletActive();
        $cariRoleUser     = User::where('id', auth()->user()->id)->first();
        $cariPermission   = Permission::where('role_id', $cariRoleUser->role_id)
            ->get();
        $menus = Menu::whereIn('id', $cariPermission->pluck('menu_id'))->get();

        return $menus->pluck('menu_name')->toArray();
    }
}
if (! function_exists('getUserIdLogin')) {
    function getUserIdLogin()
    {
        $cariUserIdLogin = User::where('id', auth()->user()->id)->first()->id;
        return $cariUserIdLogin;
    }
}
if (! function_exists('getUserOutlet')) {
    function getUserOutlet()
    {
        return UserOutlet::where('user_id', auth()->id())->pluck('outlet_id')->toArray();
    }
}
if (! function_exists('getUserInventory')) {
    function getUserInventory()
    {
        return UserInventory::where('user_id', auth()->id())->pluck('inventory_id')->toArray();
    }
}
if (! function_exists('autoCode')) {
    function autoCode($table, $field, $prefix, $length)
    {
        // Ambil nilai maksimal dari field
        $query = DB::table($table)->select(DB::raw('MAX(RIGHT(' . $field . ',' . $length . ')) as codes'))->first();
        $cd    = '';

        if ($query && $query->codes) {
            // Increment angka terakhir
            $tmp = ((int) $query->codes) + 1;
            $cd  = sprintf('%0' . $length . 's', $tmp);
        } else {
            // Jika tidak ada record, mulai dari 1
            $cd = sprintf('%0' . $length . 's', 1);
        }

        // Gabungkan prefix dan kode
        return $prefix . $cd;
    }
}
if (! function_exists('journalNumberCode')) {
    function journalNumberCode()
    {
        return Carbon::now()->format('YmdHis');
    }
}
if (! function_exists('journalClosingCode')) {
    function journalClosingCode()
    {
        $year       = substr(date('Y'), 2, 2);
        $month      = date('m');
        $day        = date('d');
        $codePrefix = 'CLOSING-' . $year . '-' . $month . '-' . $day;
        return autoCode('journal_closings', 'code', $codePrefix, 4);
    }
}
if (! function_exists('numberGroup')) {
    function numberGroup($number)
    {
        return number_format($number, 0, ',');
    }
}
if (! function_exists('opnameCode')) {
    function opnameCode($prefixCode)
    {
        $formatOpnameCode = autoCode('stock_opnames', 'code', '', 4);
        return 'OP' . '-' . $prefixCode . '-' . date('Y') . '-' . date('m') . '-' . date('d') . '-' . $formatOpnameCode;
    }
}
if (! function_exists('opnameDetailCode')) {
    function opnameDetailCode($suffic)
    {
        $formatOpnameDetailCode = autoCode('stock_opname_details', 'code', '', 4);
        return 'OPD-' . date('Y') . '-' . date('m') . '-' . date('d') . '-' . $formatOpnameDetailCode . '-' . $suffic;
    }
}
if (! function_exists('endOfDay')) {
    function endOfDay($date)
    {
        return date('Y-m-d 23:59:59', strtotime($date));
    }
}
if (! function_exists('startOfDay')) {
    function startOfDay($date)
    {
        return date('Y-m-d 00:00:00', strtotime($date));
    }
}
if (! function_exists('dateWithTime')) {
    function dateWithTime($date)
    {
        return date('d/m/Y H:i', strtotime($date));
    }
}
if (! function_exists('rupiah')) {
    function rupiah($number)
    {
        //parse semua string ke angka
        $number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);

        //jika null return 0
        if ($number == null) {
            $number = 0;
        }
        return 'Rp ' . str_replace(',000', '', number_format($number, 3, ',', '.'));
    }
}
if (! function_exists('rupiahToInteger')) {
    function rupiahToInteger($number)
    {
        $number = str_replace(',', '', $number);
        $number = str_replace('.', '', $number);
        $number = str_replace('Rp', '', $number);
        return $number;
    }
}
if (! function_exists('MyRupiah')) {
    function MyRupiah($number)
    {
        return 'Rp. ' . str_replace(',00', '', number_format($number, 2, ',', '.'));
    }
}
if (! function_exists('currency')) {
    function currency($number)
    {
        return str_replace(',00', '', number_format($number, 2, ',', '.'));
    }
}
if (! function_exists('productCategoryCode')) {
    function productCategoryCode()
    {
        $config = [
            'table'                  => 'product_categories',
            'field'                  => 'code',
            'length'                 => 8,
            'prefix'                 => 'CPC-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}
if (! function_exists('productSubCategoryCode')) {
    function productSubCategoryCode()
    {
        $config = [
            'table'                  => 'product_categories',
            'field'                  => 'code',
            'length'                 => 8,
            'prefix'                 => 'CSC-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}
if (! function_exists('saleCode')) {
    function saleCode()
    {
        $config = [
            'table'                  => 'sales',
            'field'                  => 'sale_code',
            'length'                 => 19,
            'prefix'                 => 'OUT-' . getOutletActive()->id . '-SL' . date('ymd') . '-',
            'reset_on_prefix_change' => true,

        ];
        return IdGenerator::generate($config);
    }
}
if (! function_exists('returSalesCode')) {
    function returSalesCode()
    {
        $config = [
            'table'                  => 'sales',
            'field'                  => 'sale_code',
            'length'                 => 14,
            'prefix'                 => 'RTR-' . date('y-m') . '-',
            'reset_on_prefix_change' => true,

        ];
        return IdGenerator::generate($config);
    }
}
if (! function_exists('cashierMachineCode')) {
    function cashierMachineCode()
    {
        $config = [
            'table'  => 'cashier_machines',
            'field'  => 'code',
            'length' => 8,
            'prefix' => 'kasir-',
        ];
        return IdGenerator::generate($config);
    }
}
if (! function_exists('terbilang')) {
    function terbilang($x)
    {
        $x     = abs($x);
        $angka = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp  = '';

        if ($x < 12) {
            $temp = ' ' . $angka[$x];
        } elseif ($x < 20) {
            $temp = terbilang($x - 10) . ' Belas';
        } elseif ($x < 100) {
            $temp = terbilang($x / 10) . ' Puluh' . terbilang($x % 10);
        } elseif ($x < 200) {
            $temp = ' Seratus' . terbilang($x - 100);
        } elseif ($x < 1000) {
            $temp = terbilang($x / 100) . ' Ratus' . terbilang($x % 100);
        } elseif ($x < 2000) {
            $temp = ' Seribu' . terbilang($x - 1000);
        } elseif ($x < 1000000) {
            $temp = terbilang($x / 1000) . ' Ribu' . terbilang($x % 1000);
        } elseif ($x < 1000000000) {
            $temp = terbilang($x / 1000000) . ' Juta' . terbilang($x % 1000000);
        } elseif ($x < 1000000000000) {
            $temp = terbilang($x / 1000000000) . ' Milyar' . terbilang($x % 1000000000);
        }

        return $temp;
    }
}
if (! function_exists('profileCompany')) {
    function profileCompany()
    {
        return ProfilCompany::first();
    }
}
if (! function_exists('customerCode')) {
    function customerCode()
    {
        $config = [
            'table'                  => 'customers',
            'field'                  => 'code',
            'length'                 => 10,
            'prefix'                 => 'CUST-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}
if (! function_exists('normalizePhoneNumber')) {
    function normalizePhoneNumber($phoneNumber)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '62' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }
}
if (! function_exists('customerCategoryCode')) {
    function customerCategoryCode()
    {
        $config = [
            'table'                  => 'customer_categories',
            'field'                  => 'code',
            'length'                 => 7,
            'prefix'                 => 'CC-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}

if (! function_exists('cashflowCode')) {
    function cashflowCode()
    {
        $config = [
            'table'  => 'cashflows',
            'field'  => 'code',
            'length' => 7,
            'prefix' => 'csfl-' . date('y-m') . '-',
        ];
        return IdGenerator::generate($config);
    }
}
if (! function_exists('productStockForReminder')) {
    function productStockForReminder()
    {
        $userRole = Auth::user()->role->role_name;

        if ($userRole === 'SUPERADMIN') {
            $q = Product::join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
                ->join('outlets', 'outlets.id', '=', 'product_stocks.outlet_id')
                ->join('product_units', 'product_units.id', '=', 'products.product_unit_id')
                ->where('product_stocks.stock_current', '<=', 'products.minimum_stock')
                ->limit(2000)
                ->get([
                    'products.name', 
                    'products.minimum_stock', 
                    'products.code', 
                    'product_stocks.stock_current', 
                    'outlets.name as outlet_name', 
                    'product_units.symbol as unit_name'
                ]);
        } else {
            $q = Product::join('product_stocks', 'product_stocks.product_id', '=', 'products.id')
                ->join('outlets', 'outlets.id', '=', 'product_stocks.outlet_id')
                ->join('product_units', 'product_units.id', '=', 'products.product_unit_id')
                ->where('product_stocks.outlet_id', getOutletActive()->id)
                ->where('product_stocks.stock_current', '<=', 'products.minimum_stock')
                ->limit(2000)
                ->get([
                    'products.name',
                    'products.minimum_stock',
                    'products.code',
                    'product_stocks.stock_current',
                    'outlets.name as outlet_name',
                    'product_units.symbol as unit_name'
                ]);
        }
        return $q;
    }
}

if (! function_exists('dueDateInvoice')) {
    function dueDateInvoice()
    {
        $reminderDays = env('DUE_DATE_REMINDER_DAYS', 3);

        return DB::table('sales')
            ->select(
                'sales.id',
                'sales.sale_code',
                'sales.due_date',
                'sales.final_amount',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                DB::raw('COALESCE(SUM(sales_payments.nominal_payment), 0) as total_payment')
            )
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->leftJoin('sales_payments', 'sales.id', '=', 'sales_payments.sale_id')
            ->where('sales.is_retur', 0)
            ->where('sales.due_date', '>=', now()->toDateString())
            ->where('sales.due_date', '<=', now()->addDays($reminderDays)->toDateString())
            ->groupBy('sales.id', 'sales.sale_code', 'sales.due_date', 'sales.final_amount', 'customers.name')
            ->havingRaw('total_payment < sales.final_amount')
            ->get();
    }
}

if (! function_exists('dueDateInvoice')) {
    function dueDateInvoice()
    {
        $reminderDays = env('DUE_DATE_REMINDER_DAYS', 3);

        return DB::table('sales')
            ->select(
                'sales.id',
                'sales.sale_code',
                'sales.due_date',
                'sales.final_amount',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                DB::raw('COALESCE(SUM(sales_payments.nominal_payment), 0) as total_payment')
            )
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->leftJoin('sales_payments', 'sales.id', '=', 'sales_payments.sale_id')
            ->where('sales.due_date', '>=', now()->toDateString())
            ->where('sales.due_date', '<=', now()->addDays($reminderDays)->toDateString())
            ->groupBy('sales.id', 'sales.sale_code', 'sales.due_date', 'sales.final_amount', 'customers.name')
            ->havingRaw('total_payment < sales.final_amount')
            ->get();
    }
}

if (! function_exists('checkSettingStockReminder')) {
    function checkSettingStockReminder()
    {
        $q = Setting::where('name', 'stock_alert')->first();
        return $q->value;
    }
}
if (! function_exists('dateStandar')) {
    function dateStandar($date)
    {
        return date('d F Y', strtotime($date));
    }
}
if (! function_exists('numberGroupComma')) {
    function numberGroupComma($number)
    {
        return number_format($number, 0, ',');
    }
}
if (! function_exists('myInventoryId')) {
    function myInventoryId()
    {
        $inventory = UserInventory::where('user_id', Auth::id())->first();
        return $inventory ? $inventory->inventory_id : null;
    }
}
if (! function_exists('responseAPI')) {
    function responseAPI($success, $msg, $data = null)
    {
        return [
            'success' => $success ?? false,
            'message' => $msg ?? '',
            'data'    => $data,
        ];
    }
}
if (! function_exists('readable_number')) {
    function readable_number($number, $discharge = 0, $fractional = null, $fractional_count = 1)
    {
        $int_number = intval($number);

        if ($int_number > 999) {
            $num = substr($int_number, 0, strlen($int_number) - 3);
            $discharge++;
            $fractional = substr($int_number, strlen($int_number) - 3);

            return readable_number($num, $discharge, $fractional, $fractional_count);
        }

        return $int_number . getFractional($fractional, $fractional_count) . ' ' . getDischargeString($discharge);
    }
}
if (! function_exists('getDischargeString')) {
    function getDischargeString($discharge)
    {
        $discharges = ['RB', 'JT', 'M', 'T'];
        return $discharge ? $discharges[$discharge - 1] : '';
    }
}
if (! function_exists('getFractional')) {
    function getFractional($fractional, $fractional_count)
    {
        if ($fractional) {
            $formatted_fractional = ',' . rtrim(substr($fractional, 0, $fractional_count), '0');
            return $formatted_fractional === ',' ? '' : $formatted_fractional;
        }
        return '';
    }
}
if (! function_exists('abbreviation')) {
    function abbreviation($word)
    {
        $words        = explode(' ', $word);
        $abbreviation = '';

        foreach ($words as $w) {
            $abbreviation .= substr($w, 0, 1);
        }

        return strtoupper($abbreviation);
    }
}
if (! function_exists('mutation_color')) {
    function mutation_color($status)
    {
        switch ($status) {
            case 'draft':
                return 'blue';
            case 'open':
                return 'orange';
            case 'done':
                return 'green';
            case 'void':
                return 'red';
            default:
                return null;
        }
    }
}

if (! function_exists('getMutationStatus')) {
    function getMutationStatus($status)
    {
        switch ($status) {
            case 'draft':
                return 'Draft';
            case 'open':
                return 'Belum Disetujui';
            case 'done':
                return 'Disetujui';
            case 'void':
                return 'Ditolak';
            default:
                return null;
        }
    }
}


if (! function_exists('generateSafeId')) {
    function generateSafeId()
    {
        $uniqueId = uniqid(Str::random(10), true);
        return str_replace(['.', '+', '/', '='], '', $uniqueId);
    }
}

if (! function_exists('formatToWhatsappPhone')) {
    function formatToWhatsappPhone($phoneNumber)
    {
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        if (substr($phoneNumber, 0, 1) === '+') {
            $phoneNumber = substr($phoneNumber, 1);
        }

        if (substr($phoneNumber, 0, 1) === '0') {
            return '62' . substr($phoneNumber, 1);
        }

        return '62' . substr($phoneNumber, 1);
    }
}

if (! function_exists('isTrialExpired')) {
    function isTrialExpired($profilCompany)
    {
        if ($profilCompany->start_time && $profilCompany->trial_duration) {
            $trialEnd = \Carbon\Carbon::parse($profilCompany->start_time)->addDays($profilCompany->trial_duration);

            if (\Carbon\Carbon::now()->greaterThan($trialEnd)) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('configCodeStockOpname')) {
    function configCodeStockOpname($inv_code_or_outlet_code): array
    {
        $config = [
            'table'                  => 'stock_opnames',
            'field'                  => 'code',
            'length'                 => 21,
            'prefix'                 => 'OP' . '-' . $inv_code_or_outlet_code . '-' . date('ymd') . '-',
            'reset_on_prefix_change' => true,
        ];

        return $config;
    }
}

if (! function_exists('configCodeStockOpnameDetail')) {
    function configCodeStockOpnameDetail($product_code): array
    {
        $config = [
            'table'                  => 'stock_opname_details',
            'field'                  => 'code',
            'length'                 => 21,
            'prefix'                 => 'OPD' . '-' . date('ymd') . '-' . $product_code . '-',
            'reset_on_prefix_change' => true,
        ];

        return $config;
    }
}

if (! function_exists('updateENV')) {
    function updateEnv($key, $value)
    {
        $envFile = base_path('.env');
        $str     = file_get_contents($envFile);

        $keyExists = preg_match("/^{$key}=.*/m", $str);

        if ($keyExists) {
            // Update nilai yang sudah ada
            $str = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $str);
        } else {
            // Tambahkan jika tidak ada
            $str .= "\n{$key}={$value}";
        }

        file_put_contents($envFile, $str);

        //NOTE: kalo ga work Reload konfigurasi dari .env
    }
}

if (! function_exists('returConfigCode')) {
    function returConfigCode(): array
    {
        $config = [
            'table'                  => 'sales',
            'field'                  => 'sale_code',
            'length'                 => 14,
            'prefix'                 => 'RTR-' . date('y-m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return $config;
    }
}

//purchaseDetail code + product id
if (! function_exists('purchaseDetailCode')) {
    function purchaseDetailCode($product_id): string
    {
        $config = [
            'table'                  => 'purchase_details',
            'field'                  => 'code',
            'length'                 => 13,
            'prefix'                 => 'PD-' . date('y') . '-' . date('m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config) . '-' . $product_id;
    }
}

//purchaseRequestion code
if (! function_exists('purchaseRequestionCode')) {
    function purchaseRequestionCode(): string
    {
        $config = [
            'table'                  => 'purchases',
            'field'                  => 'code',
            'length'                 => 13,
            'prefix'                 => 'PR-' . date('y') . '-' . date('m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}

//purchase order code
if (! function_exists('purchaseOrderCode')) {
    function purchaseOrderCode(): string
    {
        $config = [
            'table'                  => 'purchases',
            'field'                  => 'code',
            'length'                 => 13,
            'prefix'                 => 'PO-' . date('y') . '-' . date('m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}

//purchase reception code + outlet code/inventory code 
if (! function_exists('purchaseReceptionCode')) {
    function purchaseReceptionCode($inv_code_or_outlet_code): string
    {
        $prefix = 'PN-' . $inv_code_or_outlet_code . '-' . date('y') . '-' . date('m') . '-';

        $config = [
            'table'                  => 'purchases',
            'field'                  => 'code',
            'length'                 => strlen($prefix) + 4,
            'prefix'                 => $prefix,
            'reset_on_prefix_change' => true,
        ];

        return IdGenerator::generate($config);
    }
}



//invoicePurchase code
if (! function_exists('invoicePurchaseCode')) {
    function invoicePurchaseCode(): string
    {
        $config = [
            'table'                  => 'purchase_invoices',
            'field'                  => 'code',
            'length'                 => 14,
            'prefix'                 => 'INP-' . date('y') . '-' . date('m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}

// invoice payment code
if (! function_exists('invoicePaymentCode')) {
    function invoicePaymentCode(): string
    {
        $config = [
            'table'                  => 'invoice_payments',
            'field'                  => 'code',
            'length'                 => 13,
            'prefix'                 => 'IP-' . date('y') . '-' . date('m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}

//purchaseReturnCode
if (! function_exists('purchaseReturnCode')) {
    function purchaseReturnCode(): string
    {
        $config = [
            'table'                  => 'purchases',
            'field'                  => 'code',
            'length'                 => 14,
            'prefix'                 => 'RET-' . date('y') . '-' . date('m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}

//salesReturnCode
if (! function_exists('salesReturnCode')) {
    function salesReturnCode(): string
    {
        $config = [
            'table'                  => 'sales',
            'field'                  => 'sale_code',
            'length'                 => 14,
            'prefix'                 => 'RTR-' . date('y') . '-' . date('m') . '-',
            'reset_on_prefix_change' => true,
        ];
        return IdGenerator::generate($config);
    }
}
