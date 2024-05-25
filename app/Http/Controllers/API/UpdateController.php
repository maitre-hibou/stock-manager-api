<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class UpdateController extends Controller
{
    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validator = $this->getValidator($request);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data sent', 'errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $model = $this->getModel($request, $id);

        $model->update($validator->validated());

        return response()->json([
            'id' => $id,
        ], Response::HTTP_OK);
    }

    abstract protected function getModel(Request $request, string $id): ?Model;

    abstract protected function getValidator(Request $request): ValidatorContract;
}
