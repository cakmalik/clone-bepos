<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Outlet;
use App\Models\SalesDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Enums\ShippingStatus;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        $summary = 0;
        if (request()->ajax()) {
            $data = Sales::query()
                ->with('salesDetails', 'customer', 'outlet')
                ->whereNot('sales_type', 'join-child')
                ->where(function ($q) use ($request) {
                    $q->where([['status', $request->status]]);
                    if ($request->outlet != '-') {
                        $q->where('outlet_id', $request->outlet);
                    }
                    if ($request->input('start_date') && $request->input('end_date')) {
                        $q->where([
                            ['sale_date', '>=', startOfDay($request->start_date)],
                            ['sale_date', '<=', endOfDay($request->end_date)]
                        ]);
                    }
                })
                ->orderByDesc('id')
                ->get()->map(function ($row) {
                    $row->sale_date = Carbon::parse($row->sale_date)->format('d F Y H:i');
                    $row->final_amount_parsed = rupiah($row->final_amount);
                    return $row;
                });

            foreach ($data as $row) {
                $summary += $row->final_amount;
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer', function ($row) {
                    $dtl_cs =
                        '<a href="" id="detail_customer" data-bs-toggle="modal" data-bs-target="#modal_detail_customer"
                        data-name="' . $row->customer->name . '"
                        data-code="' . $row->customer->code . '"
                        data-phone="' . $row->customer->phone . '"
                        data-address="' . $row->customer->address . '"
                        >' . $row->customer->name . '</a>';
                    return $dtl_cs;
                })
                ->addColumn('sale_code', function ($row) {
                    $sales_detail = '<a href="sales/' . $row->sale_code . '" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Show" >' . $row->sale_code . '</a>';
                    return $sales_detail;
                })

                ->addColumn('status', function ($row) {
                    if ($row->status == 'success') {
                        $status = '<span class="badge badge-sm bg-green">Finish</span>';
                    } else if ($row->status == 'draft') {
                        $status = ' <span class="badge badge-sm bg-secondary">Draft</span>';
                    } else {
                        $status = ' <span class="badge badge-sm bg-danger">Cancel</span>';
                    }
                    return $status;
                })->addColumn('action', function ($row) {
                    $detail = '<a href="#" class="btn btn-sm btn-info"
                    id="detail_sales" data-detail="' . $row->id . '"
                    ><li class="fas fa-eye me-1" aria-hidden="true"></li>Detail</a>';
                    return $detail;
                })
                ->addColumn('summary', function ($row) {
                    // Mengembalikan nilai $summary ke dalam kolom 'summary'
                    return $row->summary;
                })
                ->rawColumns(['sale_code', 'status', 'action', 'customer', 'final'])->make(true);
        }

        $outlets = Outlet::all();
        $sales = Sales::all();
        $now = Carbon::today();

        $baseQuery = Sales::query();

        // jika superadmin tampilkan semua data sales
        if (auth()->user()->role->role_name !== 'SUPERADMIN') {
            $baseQuery->whereIn('outlet_id', getUserOutlet());
        }

        $query = Sales::where('status', 'success')->where('is_retur', false);
        if (auth()->user()->role->role_name !== 'SUPERADMIN') {
            $query->where('outlet_id', getUserOutlet());
        }
        $success = $query->count();


        $tx['daily'] = (clone $baseQuery)->where('is_retur', false)->whereDate('sale_date', $now)->count();
        $tx['weekly'] = (clone $baseQuery)->where('is_retur', false)->whereBetween('sale_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count();
        $tx['monthly'] = (clone $baseQuery)->where('is_retur', false)->whereMonth('sale_date', $now)->count();

        $totalSales = (clone $baseQuery)
            ->where('status', 'success')
            ->where('is_retur', false)
            ->whereDate('sale_date', $now)
            ->sum('final_amount');

        $totalReturns = (clone $baseQuery)
            ->where('status', 'success')
            ->where('is_retur', true)
            ->whereDate('sale_date', $now)
            ->sum('final_amount');

        $revenue = $totalSales - $totalReturns;

        $status['success'] = (clone $baseQuery)->where('status', 'success')->count();
        $status['draft'] = (clone $baseQuery)->where('status', 'draft')->count();


        return view('pages.sales.index', compact('outlets', 'sales', 'tx', 'revenue', 'status', 'summary', 'success'));
    }

    public function show($code)
    {
        $transaction = Sales::query()
            ->where('sale_code', $code)
            ->with(['salesDetails' => function ($query) {
                $query->where('status', '!=', 'void');
            }, 'customer', 'outlet'])
            ->first();
        $data = [
            'title' => 'DATA PENJUALAN',
            'transaction' => $transaction,
        ];

        return view('pages.sales.nota', $data);
    }

    public function getNota($code, $type = 'NORMAL')
    {
        $transaction = Sales::query()
            ->where('sale_code', $code)
            ->with(['salesDetails' => function ($query) {
                $query->where('status', '!=', 'void');
            }, 'customer', 'outlet', 'paymentMethod'])
            ->first();

        $data = [
            'title' => 'DATA PENJUALAN',
            'transaction' => $transaction,
            'company' => profileCompany(),
        ];

        if (
            $type == 'NORMAL'
        ) {
            return view('pages.sales.nota.normal', $data);
        } else {
            return view('pages.sales.nota.receipt', $data);
        }
    }

    public function destroy(Sales $sales)
    {
        $sales->delete();
        return redirect()->route('sales.index')->with('success', 'Data berhasil dihapus');
    }

    public function detail(Sales $sale)
    {
        $detail = SalesDetail::where('sales_id', $sale->id)->with('product', 'product.productUnit')->get()->map(function ($row) {
            $row->unit_symbol = optional($row->product->productUnit)->symbol ?? '';
            return $row;
        });
        if (request()->ajax()) {
            return response()->json([
                'data' => $detail
            ], 200);
        }
    }
    public function printLogs(Sales $sale)
    {
        $data = $sale->reprintLogs->map(function ($log) {
            return [
                'user' => $log->user->users_name,
                'reprint_time' => Carbon::parse($log->reprint_time)->format('d-m-Y H:i:s'),
            ];
        });
        if (request()->ajax()) {
            return response()->json([
                'data' => $data
            ], 200);
        }
    }
    public function export($type)
    {
        if ($type == 'pdf') {
        } else {
            // return Excel::download(new SalesExport, 'sales.xlsx');
        }
    }


    public function updateShippingStatus(Request $request)
    {
        $shipping = Sales::find($request->shipping_id);
        if ($shipping) {
            $shipping->shipping_status = ShippingStatus::from($request->status);
            $shipping->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
