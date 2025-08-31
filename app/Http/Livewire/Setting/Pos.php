<?php

namespace App\Http\Livewire\Setting;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class Pos extends Component
{
    public $stock_alert;
    public $stock_minus;
    public $superior_validation;
    public $minus_price;
    public $price_change;
    public $show_recent_sales;
    public $show_and_change_order_status;
    public $show_nominal_transaction;
    public $change_qty_direct_after_add;
    public $change_qty_popup;
    public $custom_footer;
    public $customer_price;
    public $price_crossed;
    public $rounding_enabled;
    public $show_detail_when_close_cashier;
    public $simple_purchase;
    public $show_dashboard;

    public function mount()
    {
        $this->_setData();
    }

    private function setSettingData($name, &$property)
    {
        $property = Setting::firstOrCreate(['name' => $name], ['value' => 0])->value == 1 ? true : false;
    }

    public function _setData()
    {
        $this->setSettingData('stock_alert', $this->stock_alert);
        $this->setSettingData('stock_minus', $this->stock_minus);
        $this->setSettingData('superior_validation', $this->superior_validation);
        $this->setSettingData('minus_price', $this->minus_price);
        $this->setSettingData('price_change', $this->price_change);
        $this->setSettingData('show_recent_sales', $this->show_recent_sales);
        $this->setSettingData('customer_price', $this->customer_price);
        $this->setSettingData('show_and_change_order_status', $this->show_and_change_order_status);
        $this->setSettingData('show_nominal_transaction', $this->show_nominal_transaction);
        $this->setSettingData('rounding_enabled', $this->rounding_enabled);
        $this->setSettingData('show_detail_when_close_cashier', $this->show_detail_when_close_cashier);
        $this->setSettingData('price_crossed', $this->price_crossed);
        $this->setSettingData('simple_purchase', $this->simple_purchase);
        $this->setSettingData('show_dashboard', $this->show_dashboard);
        $this->change_qty_popup = Setting::firstOrCreate(['name' => 'change_qty_popup'], ['value' => 0])->value == 1 ? 'on' : '';
        $this->change_qty_direct_after_add = Setting::firstOrCreate(['name' => 'change_qty_direct_after_add'], ['value' => 0])->value == 1 ? 'on' : '';
        $this->custom_footer = Setting::firstOrCreate(['name' => 'custom_footer'], ['value' => 1, 'desc' => '<div><strong>Terima Kasih Kunjungan Anda :)</strong></div>'])?->desc;

        session(['simple_purchase' => $this->simple_purchase]);
    }

    public function saveCustomFooter()
    {
        Setting::updateOrCreate(['name' => 'custom_footer'], ['value' => 1, 'desc' => $this->custom_footer]);
        return redirect()->route('setting.pos.index')->with('success', 'Berhasil diperbarui');
    }

    private function updateSettingData($name, $value)
    {
        Setting::updateOrCreate(['name' => $name], ['value' => $value]);

        Log::info(sprintf(
            '[SETTING] %s | %s | %s = %s',
            now()->format('Y-m-d H:i:s'),
            auth()->user()?->users_name,
            $name,
            $value ? 'true' : 'false'
        ));


        $this->dispatchBrowserEvent('updated', ['message' => 'Berhasil diperbarui']);
    }

    public function updated($propertyName)
    {
        // Check if the property name corresponds to a setting
        if (in_array($propertyName, [
            'stock_alert',
            'stock_minus',
            'superior_validation',
            'minus_price',
            'price_change',
            'show_recent_sales',
            'customer_price',
            'show_and_change_order_status',
            'show_nominal_transaction',
            'price_crossed',
            'rounding_enabled',
            'show_detail_when_close_cashier',
            'simple_purchase',
            'show_dashboard',
        ])) {
            $this->updateSettingData($propertyName, $this->$propertyName ? 1 : 0);

            if ($propertyName === 'simple_purchase') {
                session(['simple_purchase' => $this->simple_purchase]);
            }
        }
    }

    public function updatedChangeQtyDirectAfterAdd($value)
    {
        if ($value) {
            $this->change_qty_popup = '';
            Setting::where('name', 'change_qty_direct_after_add')->update(['value' => 1]);

            Setting::where('name', 'change_qty_popup')->update(['value' => 0]);
        }
        $this->dispatchBrowserEvent('updated', ['message' => 'Berhasil diperbarui']);
    }

    public function updatedChangeQtyPopup($value)
    {
        if ($value) {
            $this->change_qty_direct_after_add = '';
            Setting::where('name', 'change_qty_popup')->update(['value' => 1]);

            Setting::where('name', 'change_qty_direct_after_add')->update(['value' => 0]);
            $this->dispatchBrowserEvent('updated', ['message' => 'Berhasil diperbarui']);
        }
    }

    public function render()
    {
        return view('livewire.setting.pos');
    }
}
