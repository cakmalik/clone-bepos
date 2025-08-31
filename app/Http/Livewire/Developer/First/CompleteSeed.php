<?php

namespace App\Http\Livewire\Developer\First;

use App\Models\User;
use App\Models\Product;
use Livewire\Component;
use App\Models\Supplier;
use App\Models\ProductPrice;
use App\Models\ProductStock;
use App\Models\TieredPrices;
use Livewire\WithFileUploads;
use App\Imports\ImportProduct;
use App\Models\ProductCategory;
use App\Models\ProductSupplier;
use App\Models\ProductStockHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CompleteSeed extends Component
{

    use WithFileUploads;
    public $selectedFile;
    public $isFresh = true;

    public function mount()
    {
        // if (Auth::user()->isDeveloper() == false) {
        //     return redirect('/');
        // }

        // if (User::count() <= 1) {
        //     return redirect()->route('developer.setup');
        // }

        $this->users = User::whereHas('role', function ($query) {
            $query->where('role_name', '!=', ['DEVELOPER']);
        })
            ->get();
    }

    public function render()
    {
        return view('livewire.developer.first.complete-seed')->layout('layouts.developer');
    }

    public function refresh()
    {

        Artisan::call('migrate:fresh --seed');

        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return redirect()->route('developer.setup');
    }

    public $showInfoAccount = true;
    public $users;
    public function toggleInfoAccount()
    {
        $this->users = User::whereHas('role', function ($query) {
            $query->where('role_name', '!=', ['DEVELOPER']);
        })
            ->get();
        $this->showInfoAccount = !$this->showInfoAccount;
    }

    public $form_import = false;
    public function importProduct()
    {
        $this->form_import = !$this->form_import;
    }

    public function import()
    {
        if ($this->selectedFile) {
            // if ($this->isFresh == true || $this->isFresh == 'true') {
            //     Schema::disableForeignKeyConstraints();

            //     ProductStockHistory::truncate();
            //     ProductSupplier::truncate();
            //     Supplier::truncate();
            //     ProductStock::truncate();
            //     TieredPrices::truncate();
            //     ProductPrice::truncate();
            //     Product::truncate();
            //     ProductCategory::truncate();

            //     Schema::enableForeignKeyConstraints();
            // }

            Excel::import(new ImportProduct, $this->selectedFile->store('tmp'));
            $this->selectedFile = null;
            $this->dispatchBrowserEvent('import-run');
        }
    }
}
