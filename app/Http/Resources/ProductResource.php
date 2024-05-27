<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Xp\StockManager\Stock\Domain\MovementInterface;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'vat' => $this->vat,
            'quantity' => $this->getQuantity(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'owner' => new ProductOwnerResource($this->owner),
        ];
    }
}
