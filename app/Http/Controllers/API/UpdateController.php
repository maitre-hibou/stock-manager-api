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

abstract class UpdateController extends Controller
{
    public function __invoke(Request $request, string $id): JsonResponse
    {
        if (null === ($model = $this->getModel($request, $id))) {
            return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        if (is_string($this->getExecutionGateAbility()) && false === Gate::allows($this->getExecutionGateAbility(), $model)) {
            return response()->json(['message' => 'You are not allowed to edit this record.'], Response::HTTP_UNAUTHORIZED);
        }

        $validator = $this->getValidator($request);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data sent', 'errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $attributes = $validator->validated();
        if (null === $model->{$this->getModelOwnerField()}) {
            $attributes = array_merge($attributes, [$this->getModelOwnerField() => $request->user()->id]);
        }

        $model->update($attributes);

        return response()->json([
            'id' => $id,
        ], Response::HTTP_OK);
    }

    abstract protected function getModel(Request $request, string $id): ?Model;

    abstract protected function getValidator(Request $request): ValidatorContract;

    protected function getExecutionGateAbility(): ?string
    {
        return null;
    }

    protected function getModelOwnerField(): string
    {
        return 'owner_id';
    }
}
