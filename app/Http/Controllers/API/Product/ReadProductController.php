<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;

final class ReadProductController extends Controller
{
    public function __invoke(Product $product): ProductResource
    {
        return new ProductResource($product);
    }
}
