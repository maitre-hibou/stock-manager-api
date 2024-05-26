<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\API\DeleteController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

final class DeleteProductController extends DeleteController
{
    protected function getModel(Request $request, string $id): ?Model
    {
        return Product::find($id);
    }

    protected function getExecutionGateAbility(): ?string
    {
        return 'delete_product';
    }
}
