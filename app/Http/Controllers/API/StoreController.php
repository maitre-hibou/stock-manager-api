<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class StoreController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validator = $this->getValidator($request);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data sent', 'errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $resourceId = $this->execute($request, $validator->validated());

        return response()->json([
            'id' => $resourceId,
        ], Response::HTTP_CREATED);
    }

    abstract protected function getValidator(Request $request): ValidatorContract;

    abstract protected function execute(Request $request, array $validated = []): string;
}
