<?php

namespace App\Http\Livewire\Developer\First;

use Livewire\Component;
use App\Jobs\SetupInitializeJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class Setup extends Component
{
    public $step = 1;
    public $step_title = 'Perusahaan';

    public $selected_product_type = '';
    public $company_name = '';


    public $inventories_name = [];

    public $outlets_name = [];

    public $product_types = [
        'retail_pro' => 'Retail Pro',
        'retail_advance' => 'Retail Advance',
    ];

    public function mount()
    {
        if (Auth::user()->isDeveloper() == false) {
            return redirect('/');
        }
    }

    public function render()
    {
        return view('livewire.developer.first.setup')->layout('layouts.developer');
    }

    public function updatedSelectedProductType()
    {
        updateEnv('CURRENT_VERSION_PRODUCT', $this->selected_product_type);
    }

    public function validateNextStep()
    {
        if ($this->step == 1) {

            $this->validate([
                'company_name' => 'required',
                // 'outlet_name' => 'required',
                'selected_product_type' => 'required',
            ]);

            $this->step = 2;
            $this->step_title = 'Inventory';
            $this->inventories_name = ['Gudang-1'];

            $this->checkCanAddInventory();
            return;
        }

        if ($this->step == 2) {
            $this->validate([
                'inventories_name' => 'required'
            ]);

            $this->step = 3;
            $this->step_title = 'Outlet';
            $this->outlets_name = ['Outlet-1'];
            $this->checkCanAddOutlet();

            return;
        }
    }

    public function removeInventory($index)
    {
        unset($this->inventories_name[$index]);
        $this->inventories_name = array_values($this->inventories_name);
    }

    public function addInventory()
    {
        $this->inventories_name[] = '';
    }

    public $canAddOutlet = true;
    public function addOutlet()
    {
        $this->outlets_name[] = '';
        $this->checkCanAddOutlet();
    }

    public function removeOutlet($index)
    {
        unset($this->outlets_name[$index]);
        $this->outlets_name = array_values($this->outlets_name);
        $this->canAddOutlet = true;
    }

    public $canAddInventory = true;
    public function checkCanAddInventory()
    {
        $maxInventory = $this->selected_product_type == 'retail_advance' ? Config::get('version_advance.inventory') : Config::get('version_pro.inventory');
        $this->canAddInventory = count($this->inventories_name) < $maxInventory;
    }

    public function checkCanAddOutlet()
    {
        $maxOutlet = $this->selected_product_type == 'retail_advance' ? Config::get('version_advance.outlet_max') : Config::get('version_pro.outlet_max');
        $this->canAddOutlet = count($this->outlets_name) < $maxOutlet;
    }

    public function backStep()
    {
        if ($this->step == 3) {
            $this->step = 2;
            $this->step_title = 'Inventory';
            return;
        }

        if ($this->step == 2) {
            $this->step = 1;
            $this->step_title = 'Perusahaan';
            return;
        }
    }

    public function generate()
    {
        SetupInitializeJob::dispatch($this->company_name, $this->selected_product_type, $this->inventories_name, $this->outlets_name);
        \Log::info('Redirecting to developer.complete-seed');
        return $this->redirectRoute('developer.complete-seed');
    }
}
