<?php

namespace App\Http\Livewire\Product\TieredPrice;

use App\Models\Outlet;
use App\Models\Product;
use Livewire\Component;
use App\Models\TieredPrices;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;

use function PHPUnit\Framework\isEmpty;

class Form extends Component
{
    public Product $product;
    public $outlet_id = null;
    public $as_new_price = false; //bool
    public $outlets;
    public $tiers = [];
    public $globalOutletId = null;

    public function mount()
    {
        if ($this->as_new_price == false) {
            $this->globalOutletId = $this->outlet_id;
        }
    }

    public function addTier()
    {
        $this->tiers[] = [
            'id' => null,
            'min' => null,
            'max' => null,
            'price' => null,
            'outlet_id' => null,
        ];
    }

    public $removedTier = [];

    private function validateRanges($ranges)
    {
        // Sorting dengan valu miniimmal
        usort($ranges, function ($a, $b) {
            return $a['min'] - $b['min'];
        });

        $length = count($ranges);

        for ($i = 0; $i < $length - 1; $i++) {
            // Memeriksa apakah ada overlap
            if ($ranges[$i]['max'] >= $ranges[$i + 1]['min']) {
                return false; // Terdapat overlap
            }
        }

        return true; // Tidak ada overlap
    }


    private function validateMinMax($ranges)
    {
        foreach ($ranges as $range) {
            if ($range['max'] < $range['min']) {
                return false; // Hanya jika max < min saja yang tidak valid
            }
        }
        return true;
    }
    // private function validateMinMax($ranges)
    // {
    //     foreach ($ranges as $range) {
    //         // Memeriksa apakah nilai 'max' lebih besar dari 'min'
    //         if ($range['max'] <= $range['min']) {
    //             return false; // Terdapat nilai 'max' yang tidak valid
    //         }
    //     }
    //
    //     return true; // Semua nilai 'max' lebih besar dari 'min'
    // }

    private function validationForm()
    {


        $this->validate(
            [
                'tiers.*.min' => 'required|numeric',
                'tiers.*.max' => 'required|numeric|gte:tiers.*.min',
                'tiers.*.price' => 'required',
            ],
            [
                'tiers.*.min.required' => 'Periksa',
                'tiers.*.min.numeric' => 'Periksa',
                'tiers.*.max.required' => 'Periksa',
                'tiers.*.max.numeric' => 'Periksa',
                'tiers.*.max.gte' => 'Periksa',
                'tiers.*.price.required' => 'Periksa',
            ],
        );
    }
    public function save()
    {
        $this->validationForm();

        if ($this->as_new_price == true) {
            $this->saveAsNew();
            return;
        }

        if ($this->validateRanges($this->tiers)) {
            if ($this->validateMinMax($this->tiers)) {
                if ($this->removedTier) {
                    foreach ($this->removedTier as $key) {
                        if ($key != null) {
                            $find = TieredPrices::find($key);
                            $find->delete();
                        }
                    }
                }

                if (count($this->tiers) > 0) {
                    foreach ($this->tiers as $tier) {

                        $price = rupiahToInteger($tier['price']);
                        // jika id null, menambahkan data baru artinya data tsb datang dr db
                        if ($tier['id'] == null) {
                            $this->product?->tieres()->create([
                                'outlet_id' => $this->globalOutletId ?: null,
                                'min_qty' => $tier['min'],
                                'max_qty' => $tier['max'],
                                'price' => $price,
                            ]);
                        } else {
                            $prd = TieredPrices::find($tier['id']);
                            $prd->update([
                                'outlet_id' =>  $this->globalOutletId,
                                'min_qty' => $tier['min'],
                                'max_qty' => $tier['max'],
                                'price' => $price,
                            ]);
                        }
                    }
                } else {
                    if ($this->outlet_id != null) {
                        $this->product->tieres()->where('outlet_id', $this->outlet_id)->delete();
                    }
                    if ($this->outlet_id == null) {
                        $this->product->tieres()->whereNull('outlet_id')->delete();
                    }
                }
                $this->dispatchBrowserEvent('updated-tier', ['message' => 'Berhasil disimpan    ']);
                $this->tiers = [];
                $this->allow_empty = false;
                $this->product?->load('tieres');
                $this->render();
                $this->histories();
                $this->closeForm();
            } else {
                $this->addError('tiers', 'Terdapat range yang tidak valid.');
            }
        } else {
            $this->addError('tiers', 'Terdapat Nilai Minimal atau Maksimal yang tidak valid!');
        }
    }

    public $allow_empty = false;

    public function removeTier($key)
    {
        $this->allow_empty = true;
        //kalo ada push aja dlu ke removed, karena butuh buat log
        if ($this->tiers[$key]['id'] != null) {
            $this->removedTier[] = $this->tiers[$key]['id'];
        }

        unset($this->tiers[$key]);
        $this->tiers = array_values($this->tiers);
    }
    public function render()
    {
        $this->outlets = Outlet::get();
        // $this->outlets = Outlet::when($this->outlet_id != null, function ($q) {
        //     $q->where('id', '!=', $this->outlet_id);
        // })->get();

        if ($this->tiers == [] && !$this->allow_empty) {
            $this->setTiers();
        }

        return view('livewire.product.tiered-price.form');
    }

    public function setTiers()
    {
        // $this->globalOutletId = $this->outlet_id;
        $tiers = TieredPrices::where('outlet_id', $this->outlet_id)
            ->where('product_id', $this->product?->id)
            ->get();
        if ($tiers->count() > 0 && $this->as_new_price == false) {
            foreach ($tiers as $tier) {
                $this->tiers[] = [
                    'id' => $tier->id,
                    'outlet_id' => $tier->outlet_id,
                    'outlet_name' => $tier->outlet->name ?? null,
                    'min' => $tier->min_qty,
                    'max' => $tier->max_qty,
                    'price' => $tier->price,
                ];
            }
        }
    }

    public $logs;
    public $showLog = false;
    public function histories()
    {
        if ($this->as_new_price) return;

        $la = Activity::where(function ($query) {
            $query->whereIn('subject_id', $this->product->tieres()->pluck('id'))
                ->orWhereJsonContains('properties->old->product_id', $this->product->id);
        })
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();
        if (!$la) {
            $this->dispatchBrowserEvent('logs-null', ['message' => 'Data Log Kosong']);
            return;
        } else {
            $this->logs = $la;
        }
    }

    public function toggleLog()
    {
        $this->histories();
        $this->showLog = !$this->showLog;
    }

    public function saveAsNew()
    {
        if (count($this->tiers) == 0) {
            $this->dispatchBrowserEvent('updated-tier', ['message' => 'FORM TIDAK BOLEH KOSONG']);
            return;
        }

        $this->validationForm();
        if ($this->checkIsExistTier()) {
            $this->dispatchBrowserEvent('updated-tier', ['message' => 'Sudah ada harga bertingkat untuk outlet terpilih']);
            return;
        }

        foreach ($this->tiers as $tier) {
            $price = rupiahToInteger($tier['price']);
            // jika id null, menambahkan data baru artinya data tsb datang dr db
            if ($tier['id'] == null) {
                // $this->product?->tieres()->create([
                //     'outlet_id' => $this->globalOutletId ?: null,
                //     'min_qty' => $tier['min'],
                //     'max_qty' => $tier['max'],
                //     'price' => $price,
                // ]);
                TieredPrices::Create([
                    'outlet_id' => $this->globalOutletId == '' ? null : $this->globalOutletId,
                    'product_id' => $this->product->id,
                    'min_qty' => $tier['min'],
                    'max_qty' => $tier['max'],
                    'price' => $price,
                ]);

                $this->dispatchBrowserEvent('updated-tier', ['message' => 'Berhasil disimpan!']);
                $this->tiers = [];
                $this->allow_empty = false;
                $this->product?->load('tieres');
                $this->render();
                $this->histories();
                $this->closeForm();
            } else {
                $prd = TieredPrices::find($tier['id']);
                $prd->update([
                    'outlet_id' => $this->globalOutletId ?: null,
                    'min_qty' => $tier['min'],
                    'max_qty' => $tier['max'],
                    'price' => $price,
                ]);
            }
        }
        return;
    }

    public function closeForm()
    {
        $this->tiers = [];
        $this->emitUp('close');
    }

    private function checkIsExistTier(): bool
    {
        // $outlet = Outlet::where('id', $this->globalOutletId)->first();
        // dd($outlet, $this->product->id);
        return TieredPrices::where('outlet_id', $this->globalOutletId)
            ->where('product_id', $this->product?->id)->exists();
    }
}
