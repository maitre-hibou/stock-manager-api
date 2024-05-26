<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

abstract class SubresourceStoreController extends Controller
{
    public function __invoke(Request $request, string $id): JsonResponse
    {
        if (null === ($parentModel = $this->getParentModel($request, $id))) {
            return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        if (is_string($this->getExecutionGateAbility()) && false === Gate::allows($this->getExecutionGateAbility(), $parentModel)) {
            return response()->json(['message' => 'You are not allowed to edit this record.'], Response::HTTP_UNAUTHORIZED);
        }

        $validator = $this->getValidator($request);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data sent', 'errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $resourceId = $this->execute($request, $parentModel, $validator->validated());

        return response()->json([
            'id' => $resourceId,
        ], Response::HTTP_CREATED);
    }

    abstract protected function getValidator(Request $request): ValidatorContract;

    abstract protected function getParentModel(Request $request, string $id): ?Model;

    abstract protected function execute(Request $request, Model $parentModel, array $validated = []): string;

    protected function getExecutionGateAbility(): ?string
    {
        return null;
    }
}
