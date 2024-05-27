<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Xp\StockManager\Catalog\Domain\ProductInterface;
use Xp\StockManager\Stock\Domain\MovementInterface;

/**
 * @property string $title
 * @property string $description
 * @property int $price
 * @property float $vat
 */
class Product extends Model implements ProductInterface
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title', 'description', 'price', 'vat', 'owner_id',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getQuantity(): int
    {
        $quantity = 0;
        /** @var StockMovement $stockMovement */
        foreach ($this->stockMovements as $stockMovement) {
            $quantity = match($stockMovement->direction) {
                MovementInterface::DIRECTION_IN => $quantity += $stockMovement->quantity,
                MovementInterface::DIRECTION_OUT => $quantity -= $stockMovement->quantity,
            };
        }

        return $quantity;
    }
}
