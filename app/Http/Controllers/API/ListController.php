<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

abstract class ListController extends Controller
{
    protected const int PER_PAGE = 10;
}
