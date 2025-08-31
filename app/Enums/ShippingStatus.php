<?php

namespace App\Enums;

enum ShippingStatus: string
{
    case PENDING = 'pending';
        // case IN_TRANSIT = 'in_transit';
    case OUT_FOR_DELIVERY = 'out_for_delivery';
    // case DELIVERED = 'delivered';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'BELUM DIKIRIM',
            // self::IN_TRANSIT => 'In Transit',
            self::OUT_FOR_DELIVERY => 'DIKIRIM',
            // self::DELIVERED => 'Delivered'
        };
    }
}
