<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Product\StockMovement;

use App\Http\Controllers\API\SubresourceStoreController;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Xp\StockManager\Stock\Domain\MovementDirection;

final class StoreProductStockMovementController extends SubresourceStoreController
{
    protected function execute(Request $request, Model $parentResource, array $validated = []): string
    {
        $stockMovement = StockMovement::create([
            'product_id' => $parentResource->id,
            'direction' => $validated['direction'],
            'quantity' => $validated['quantity'],
        ]);

        return (string) $stockMovement->id;
    }

    protected function getValidator(Request $request): ValidatorContract
    {
        return Validator::make($request->all(), [
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

    protected function getParentModel(Request $request, string $id): ?Model
    {
        return Product::find($id);
    }

    protected function getExecutionGateAbility(): ?string
    {
        return 'edit_product';
    }
}
