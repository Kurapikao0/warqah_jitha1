<?php

namespace App\Enums;

enum ProductAttributeInputType: string
{
    case Text = 'text';
    case Number = 'number';
    case Select = 'select';
    case Color = 'color';
    case Boolean = 'boolean';
}
