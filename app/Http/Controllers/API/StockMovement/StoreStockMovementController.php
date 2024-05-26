<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\StockMovement;

use App\Http\Controllers\API\StoreController;
use App\Models\StockMovement;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Xp\StockManager\Stock\Domain\MovementDirection;

final class StoreStockMovementController extends StoreController
{
    protected function execute(Request $request, array $validated = []): string
    {
        $stockMovement = StockMovement::create([
            'product_id' => $validated['product_id'],
            'direction' => $validated['direction'],
            'quantity' => $validated['quantity'],
        ]);

        return (string) $stockMovement->id;
    }

    protected function getValidator(Request $request): ValidatorContract
    {
        return Validator::make($request->all(), [
            'product_id' => [
                'required',
                'exists:products,id',
            ],
            'direction' => [
                'required',
                Rule::enum(MovementDirection::class)
            ],
            'quantity' => [
                'required',
                'min:1',
            ],
        ]);
    }
}
