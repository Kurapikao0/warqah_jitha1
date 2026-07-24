<?php

namespace App\Enums;

enum ProductType: string
{
    case FinishedGood = 'finished_good';
    case RawMaterial = 'raw_material';
    case SemiFinished = 'semi_finished';
}
