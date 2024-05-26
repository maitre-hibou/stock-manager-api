<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\StockMovement;

use App\Http\Controllers\API\ListController;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class ListStockMovementController extends ListController
{
    public function __invoke(): ResourceCollection
    {
        return StockMovementResource::collection(
            StockMovement::paginate(self::PER_PAGE)
        );
    }
}
