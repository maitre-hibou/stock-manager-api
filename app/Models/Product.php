<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Xp\StockManager\Catalog\Domain\ProductInterface;

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
}
