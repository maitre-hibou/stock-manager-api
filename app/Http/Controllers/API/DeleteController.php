<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

abstract class DeleteController extends Controller
{
    public function __invoke(Request $request, string $id): JsonResponse|Response
    {
        if (null === ($model = $this->getModel($request, $id))) {
            return response()->json(['message' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        if (is_string($this->getExecutionGateAbility()) && false === Gate::allows($this->getExecutionGateAbility(), $model)) {
            return response()->json(['message' => 'You are not allowed to remove this record.'], Response::HTTP_UNAUTHORIZED);
        }

        $model->delete();

        return response()->noContent(Response::HTTP_OK);
    }

    abstract protected function getModel(Request $request, string $id): ?Model;

    protected function getExecutionGateAbility(): ?string
    {
        return null;
    }
}
