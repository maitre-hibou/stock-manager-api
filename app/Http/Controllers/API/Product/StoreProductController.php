<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\API\StoreController;
use App\Models\Product;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class StoreProductController extends StoreController
{
    protected function execute(Request $request, array $validated = []): string
    {
        $product = Product::create([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'price' => $request->get('price'),
            'vat' => $request->get('vat'),
            'owner_id' => $request->user()->id,
        ]);

        return $product->id;
    }

    protected function getValidator(Request $request): ValidatorContract
    {
        return Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'filled',
            'price' => 'required',
            'vat' => 'required',
        ]);
    }
}
