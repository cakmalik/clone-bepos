<?php

namespace App\Enums;

enum MutationCategory: string
{
    case INVENTORY_TO_OUTLET = 'inventory_to_outlet';
    case INVENTORY_TO_INVENTORY = 'inventory_to_inventory';
    case OUTLET_TO_OUTLET = 'outlet_to_outlet';
    case OUTLET_TO_INVENTORY = 'outlet_to_inventory';

    public function label(): string
    {
        return match ($this) {
            self::INVENTORY_TO_OUTLET => 'Gudang ke Outlet',
            self::INVENTORY_TO_INVENTORY => 'Gudang ke Gudang',
            self::OUTLET_TO_OUTLET => 'Outlet ke Outlet',
            self::OUTLET_TO_INVENTORY => 'Outlet ke Gudang',
        };
    }
}
