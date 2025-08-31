<?php

namespace App\Http\Controllers\API\Minimarket;

use App\Enums\PaymentStatus;
use App\Enums\ShippingStatus;
use App\Http\Controllers\Controller;
use App\Models\Sales;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function paymentStatusList()
    {
        return response()->json(
            [
                'success' => true,
                'status' => 'success',
                'data' => collect(PaymentStatus::cases())->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ],
            200,
        );
    }

    public function shippingStatusList()
    {
        return response()->json(
            [
                'success' => true,
                'status' => 'success',
                'data' => collect(ShippingStatus::cases())->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ],
            200,
        );
    }

    public function updateShippingStatusSales(Sales $sales, Request $request)
    {
        $sales->shipping_status = $request->shipping_status;
        $sales->save();

        $status = ShippingStatus::from($request->shipping_status);
        return response()->json(
            [
                'success' => true,
                'status' => 'success',
                'message' => 'Status changed to: ' . $status->label(),
            ]
        );
    }
}
