<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Sales;
use App\Models\SalesDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenController extends Controller
{
    public function orderLists(Request $request)
    {
        $perPage = $request->query('per_page') ?? 20;
        $processStatus = $request->query('process_status');
        $items = Sales::with(['salesDetails.product', 'salesDetails.handler', 'creator'])
            ->when($processStatus, function ($query) use ($processStatus) {
                return $query->where('process_status', $processStatus);
            })
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'status' => 'success',
            'data' => $items
        ]);
    }

    public function updateDetailStatus(Request $request)
    {
        $validated = $request->validate([
            'sales_details_id' => 'array',
            'sales_details_id.*' => 'required|exists:sales_details,id',
            'process_status' => 'required|in:waiting,inprogress,done,served,cancel',
        ]);

        DB::beginTransaction();
        try {
            $sales_ids = [];
            foreach ($validated['sales_details_id'] as $id) {
                $sale_detail = SalesDetail::find($id);
                $sale_detail->process_status = $validated['process_status'];
                if ($validated['process_status'] == 'inprogress' || $validated['process_status'] == 'done') {
                    $sale_detail->handler_user_id = Auth::user()->id;
                }
                $sales_ids[] = $sale_detail->sales_id;
                $sale_detail->save();
            }

            $sales_ids = array_unique($sales_ids);
            foreach ($sales_ids as $sale_id) {
                $sale = Sales::find($sale_id);
                $sales_details = SalesDetail::where('sales_id', $sale_id)->get()->groupBy('process_status');
                $count_waiting = sizeof($sales_details['waiting'] ?? []);
                $count_inprogress = sizeof($sales_details['inprogress'] ?? []);
                $count_done = sizeof($sales_details['done'] ?? []);
                $count_served = sizeof($sales_details['served'] ?? []);
                $count_cancel = sizeof($sales_details['cancel'] ?? []);

                if ($count_waiting && !$count_inprogress && !$count_done && !$count_served) {
                    $sale->process_status = 'waiting';
                } else if ($count_inprogress && !$count_done) {
                    $sale->process_status = 'inprogress';
                } else if (($count_waiting || $count_inprogress) && $count_done) {
                    $sale->process_status = 'some';
                } else if (!$count_waiting && !$count_inprogress && $count_done) {
                    $sale->process_status = 'done';
                } else if (!$count_done && $count_served) {
                    $sale->process_status = 'served';
                } else if (!$count_waiting && !$count_inprogress && !$count_done && !$count_served && $count_cancel) {
                    $sale->process_status = 'cancel';
                }
                $sale->save();
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
               'status' => 'error',
               'message' => $e->getMessage(),
            ]);
        }
    }
}

