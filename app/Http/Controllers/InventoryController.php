<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ProductStock;
use App\Models\UserInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class InventoryController extends Controller
{
    public $types = [
        [
            'text' => 'Gudang',
            'value' => 'gudang',
            'prefix' => 'GUD-'
        ],
        [
            'text' => 'Onhand',
            'value' => 'onhand',
            'prefix' => 'ONH-'
        ],
        [
            'text' => 'Ontransit',
            'value' => 'ontransit',
            'prefix' => 'ONT-'
        ]
    ];

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $filterType = $request->get('type');
            $status = $request->get('status');
            $inventories = Inventory::with('parent')
                ->when($filterType, function ($query) use ($filterType) {
                    return $query->where('type', $filterType);
                })
                ->when($status == 'active' || $status == 'inactive', function ($query) use ($status) {
                    return $query->where('is_active', $status == 'active' ? 1 : 0);
                })
                ->get();
            return DataTables::of($inventories)
                ->addIndexColumn()
                ->make(true);
        }

        return view('pages.inventory.index')
            ->with('types', $this->types);
    }

    public function create(Request $request)
    {
        $parents = Inventory::where('is_active', 1)->where('is_parent', 1)->get();

        return view('pages.inventory.create')
            ->with('parents', $parents)
            ->with('types', $this->types);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        $validated = $request->validate([
            'parent_id' => [
                'nullable',
                Rule::exists('inventories', 'id')->where(function ($query) {
                    return $query->where('is_parent', 1);
                }),
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_column($this->types, 'value')),
            'is_parent' => 'nullable|boolean',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);


        try {
            if (isset($validated['is_parent']) && $validated['is_parent'] && isset($validated['parent_id'])) {
                unset($validated['parent_id']);
            }
            $validated['code'] = autoCode('inventories', 'code', $this->getCodePrefix($validated['type']), 4);
            Inventory::create($validated);
            DB::commit();

            return redirect()->route('inventory.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data gagal ditambahkan.');
        }
    }

    public function getCodePrefix($find)
    {
        foreach ($this->types as $type) {
            if ($type['value'] === $find) {
                return $type['prefix'];
            }
        }
        return null;
    }

    public function edit(Request $request, $id)
    {
        $parents = Inventory::where('is_active', 1)->where('is_parent', 1)->get();
        $inventory = Inventory::where('id', $id)->with('parent')->first();
        return view('pages.inventory.edit')
            ->with('inventory', $inventory)
            ->with('parents', $parents)
            ->with('types', $this->types);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $validated = $request->validate([
            'parent_id' => [
                'nullable',
                Rule::exists('inventories', 'id')->where(function ($query) {
                    return $query->where('is_parent', 1);
                }),
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_column($this->types, 'value')),
            'is_parent' => 'nullable|boolean',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);


        try {
            
            $inventory = Inventory::where('id', $id)->first();

            if ($inventory->type != $validated['type']) {
                $inventory->type = $validated['type'];
                $inventory->code = autoCode('inventories', 'code', $this->getCodePrefix($validated['type']), 4);
            }
            if (isset($validated['is_parent']) && $validated['is_parent']) {
                if (isset($validated['parent_id'])) {
                    unset($validated['parent_id']);
                }
                $inventory->parent_id = null;
                $inventory->is_parent = 1;
            } else {
                if (!isset($validated['parent_id'])) {
                    return redirect()->back()->with('error', 'Pilih Gudang Induk.');
                }
                $inventory->parent_id = $validated['parent_id'];
                $inventory->is_parent = 0;
            }
            $inventory->name = $validated['name'];
            $inventory->description = $validated['description'];
            $inventory->is_active = $validated['is_active'] ?? false;
            $inventory->save();
            DB::commit();
            return redirect()->route('inventory.index')->with('success', 'Data berhasil diubah.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Data gagal diubah.');
        }
    }

    public function destroy($id)
    {
        try {

            $userInventory = UserInventory::where('inventory_id', $id)->first();
            if ($userInventory) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Data tidak bisa dihapus! terdapat data user di gudang.'
                ], 422);
            }


            $productStock = ProductStock::where('inventory_id', $id)->first();

            if ($productStock) {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Data tidak bisa dihapus! terdapat data stok'
                ], 422);
            }
            
            Inventory::where('id', $id)->delete();

            return redirect()->route('inventory.index')
                ->with('success', 'Data berhasil dihapus!');

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'success'   => false,
                'message'   => 'Data tidak bisa dihapus!, Terjadi kesalahan di server'
            ], 500);
        }
    }
}
