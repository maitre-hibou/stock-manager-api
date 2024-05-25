<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\API\ListController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ListProductController extends ListController
{
    public function __invoke(Request $request): ResourceCollection
    {
        return ProductResource::collection(
            Product::paginate(perPage: self::PER_PAGE)
        );
    }
}
