<?php

namespace App\Http\Livewire\Product;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;


class CetakBarcode extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $checkAll = false;
    public $selectedProducts = [];
    public $selectAll = false;

    private $products;
    public $cari = '';
    public $selectedBarcode;
    public $productName;
    public $jumlah;

    public function mount()
    {
        $this->products = Product::with('productStock')
            // ->where('outlet_id', getOutletActive()->id)
            ->orderByDesc('created_at')->paginate(20);
    }

    public function updatingCari()
    {
        $this->resetPage();
    }

    public function render()
    {
        if ($this->cari) {
            $products = Product::with('productStock')
                // ->where('outlet_id', getOutletActive()->id)
                ->where('name', 'like', '%' . $this->cari . '%')
                ->orWhere('barcode', 'like', '%' . $this->cari . '%')
                ->orWhere('code', 'like', '%' . $this->cari . '%')
                ->select('id', 'name', 'barcode', 'code')
                ->paginate(20);
        } else {
            $products = Product::with('productStock')
                // ->where('outlet_id', getOutletActive()->id)
                ->orderByDesc('created_at')->paginate(20);
        }

        return view('livewire.product.cetak-barcode', [
            'products' => $products
        ]);
    }

    public function selectedId($v)
    {
        $this->selectedBarcode = $v['barcode'];
        $this->productName = $v['name'];
    }

    public function toggleSelect($productId)
    {
        if (in_array($productId, $this->selectedProducts)) {
            // dd($productId);
            // dd('bb');
            $this->selectedProducts = array_diff($this->selectedProducts, [$productId]);
        } else {
            // dd('aa');
            $this->selectedProducts[] = $productId;
        }
    }

    public function toggleAll()
    {
        if ($this->selectAll) {
            $this->selectedProducts = [];
        } else {
            $this->selectedProducts = Product::pluck('id')->toArray(); // Ganti dengan model dan field yang sesuai
        }

        $this->selectAll = !$this->selectAll;
    }



    public function cetak()
    {
        $this->validate([
            'jumlah' => 'required|numeric|min:1'
        ]);

        $pdf = PDF::loadView('pages.product.product_all.barcode-pdf', [
            'barcode' => (int)$this->selectedBarcode,
            'jumlah' => (int)$this->jumlah,
            'name' => $this->productName
        ])->output();

        $filename = $this->productName . ' (' . $this->jumlah . ')' . '.pdf';
        return response()->streamDownload(
            fn () => print($pdf),
            $filename
        );
    }

    public function cetakPriceTag()
    {
        $selectedProducts = $this->selectedProducts;
        $encodedProducts = json_encode($selectedProducts);
        return redirect()->route('product.price-tag', ['selectedProducts' => $encodedProducts]);
    }
}
