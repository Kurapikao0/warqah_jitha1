<?php

namespace App\Enums;

enum OrderType: string
{
    case ReadyMade = 'ready_made';
    case Custom = 'custom';
    case Mixed = 'mixed';
}
