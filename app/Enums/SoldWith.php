<?php

namespace App\Enums;

enum SoldWith: string
{
    case NORMAL_PRICE = 'normal_price';
    case TIERED_PRICE = 'tiered_price';
    case SPECIAL_OUTLET_PRICE = 'special_outlet_price';
    case CUSTOMER_CATEGORY = 'customer_category';


    public function label(): string
    {
        return match ($this) {
            self::NORMAL_PRICE => 'Harga Normal',
            self::TIERED_PRICE => 'Harga Tiered',
            self::SPECIAL_OUTLET_PRICE => 'Harga Special Outlet',
            self::CUSTOMER_CATEGORY => 'Kategori Customer',
        };
    }
}
