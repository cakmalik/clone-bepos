<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Table;
use Illuminate\Http\Request;

class OutletTableController extends Controller
{
    public function index($outlet_id)
    {
        try {
            $outlet = Outlet::where('id', $outlet_id)->with('tables', function ($q) {
                return $q->orderBy('name');
            })->first();;
            return view('pages.outlet-table.index', compact('outlet'));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name' => 'required|unique:tables,name|regex:/^[a-zA-Z0-9\s]+$/'
        ]);

        try {
            Table::create($validated);
            return redirect()->route('outlet-table.index', [$validated['outlet_id']])->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal ditambahkan' . $th->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $table = Table::findOrFail($id);
            $table->delete();
            return redirect()->route('outlet-table.index', [$table->outlet_id])->with('success', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal dihapus' . $th->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|unique:tables,name,' . $id . '|regex:/^[a-zA-Z0-9\s]+$/'
        ]);

        try {
            $table = Table::findOrFail($id);
            $table->name = $validated['name'];
            $table->save();
            return redirect()->route('outlet-table.index', [$table->outlet_id])->with('success', 'Data berhasil diubah');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Data gagal diubah' . $th->getMessage());
        }
    }
}
