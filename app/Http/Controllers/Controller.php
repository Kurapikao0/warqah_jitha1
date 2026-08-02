<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\ApiResponse;

abstract class Controller
{
    use AuthorizesRequests;
    use ApiResponse;
}