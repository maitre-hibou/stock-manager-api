<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Xp\StockManager\Stock\Domain\MovementInterface;

class StockMovement extends Model implements MovementInterface
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id', 'direction', 'quantity',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getUpdatedAtColumn(): ?string
    {
        return null;
    }
}
