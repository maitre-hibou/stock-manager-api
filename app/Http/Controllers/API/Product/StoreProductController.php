<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\API\StoreController;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Xp\StockManager\Stock\Domain\MovementInterface;

final class StoreProductController extends StoreController
{
    protected function execute(Request $request, array $validated = []): string
    {
        $product = Product::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'price' => (int) $validated['price'],
            'vat' => $validated['vat'],
            'owner_id' => $request->user()->id,
        ]);

        if (array_key_exists('quantity', $validated)) {
            StockMovement::create([
                'product_id' => $product->id,
                'direction' => MovementInterface::DIRECTION_IN,
                'quantity' => $validated['quantity'],
            ]);
        }

        return $product->id;
    }

    protected function getValidator(Request $request): ValidatorContract
    {
        return Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'filled',
            'price' => 'required',
            'vat' => 'required',
            'quantity' => [
                'filled',
                'min:1',
            ],
        ]);
    }
}
