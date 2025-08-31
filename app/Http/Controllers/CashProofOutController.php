<?php

namespace App\Http\Controllers;

use App\Models\CashMaster;
use App\Models\CashProof;
use App\Models\CashProofItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class CashProofOutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $proofs = CashProofItem::with('cashProof', 'cashMaster')
                ->whereHas('cashProof', function ($query) use ($start_date, $end_date) {
                    $query->when($start_date, function ($query) use ($start_date) {
                        $query->whereDate('date', '>=', $start_date);
                    })->when($end_date, function ($query) use ($end_date) {
                        $query->whereDate('date', '<=', $end_date);
                    })
                    ->where('type', 'KAS-KELUAR');
                })
                ->get();
            return DataTables::of($proofs)
                ->addIndexColumn()
                ->addColumn('preview_url', function ($item) {
                    return route('cashProofOut.print', $item->cashProof->id);
                })
                ->make(true);
        }
        return view('pages.cash_proof.out.index');
    }

    public function create()
    {
        $cashMasters = CashMaster::all();
        return view('pages.cash_proof.out.create', compact('cashMasters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'received_from' => 'required|string',
            'items' => 'required|array',
            'items.*.cash_master_id' => 'required|exists:cash_masters,id',
            'items.*.ref_code' => 'required',
            'items.*.description' => 'required',
            'items.*.nominal' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            $proof = CashProof::create([
                'received_from' => $validated['received_from'],
                'date' => date('Y-m-d H:i:s'),
                'code' => autoCode('cash_proofs', 'code', 'BKK-1-' . date('y') . '-' . date('m') . '-', 4),
                'type' => 'KAS-KELUAR',
            ]);

            foreach ($validated['items'] as $item) {
                CashProofItem::create([
                    'cash_proof_id' => $proof->id,
                    'ref_code' => $item['ref_code'],
                    'description' => $item['description'],
                    'nominal' => $item['nominal'],
                    'cash_master_id' => $item['cash_master_id'],
                ]);
            }

            DB::commit();
            return redirect()->route('cashProofOut.print', [$proof->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Data gagal ditambahkan');
        }
    }

    public function print(Request $request, $id)
    {
        $proof = CashProof::findOrFail($id);
        return view('pages.cash_proof.out.print', compact('proof'));
    }

    public function receipt(Request $request, $id)
    {
        $proof = CashProof::findOrFail($id);
        $proof->load(['items.cashMaster']);
        $total = collect($proof->items)->sum('nominal');
        return view('pages.cash_proof.out.receipt', compact('proof', 'total'));
    }
}
