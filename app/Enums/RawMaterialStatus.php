<?php

namespace App\Enums;

enum RawMaterialStatus: string
{
    case InStock = 'in_stock';
    case LowStock = 'low_stock';
    case OutOfStock = 'out_of_stock';
}
