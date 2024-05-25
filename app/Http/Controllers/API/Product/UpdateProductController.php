<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\API\UpdateController;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Request;

final class UpdateProductController extends UpdateController
{
    protected function getValidator(Request $request): ValidatorContract
    {
        return Validator::make($request->all(), [
            'title' => 'filled',
            'description' => 'filled',
            'price' => 'filled',
            'vat' => 'filled',
        ]);
    }

    protected function getModel(Request $request, string $id): ?Model
    {
        return Product::find($id);
    }

    protected function getExecutionGate(): ?string
    {
        return 'edit_product';
    }
}
