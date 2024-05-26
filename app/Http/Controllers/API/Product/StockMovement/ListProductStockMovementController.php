<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Product\StockMovement;

use App\Http\Controllers\API\ListController;
use App\Http\Resources\StockMovementSubResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ListProductStockMovementController extends ListController
{
    public function __invoke(Request $request, Product $product): ResourceCollection
    {
        return StockMovementSubResource::collection(
            $product->stockMovements()->orderByDesc('created_at')->paginate(self::PER_PAGE)
        );
    }
}
