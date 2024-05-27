<?php

namespace App\Listeners;

use App\Events\ProductLowStock;
use App\Mail\ProductLowStockNotification;
use Illuminate\Support\Facades\Mail;

class ProductStockListener
{
    /**
     * Handle the event.
     */
    public function handle(ProductLowStock $event): void
    {
        $product = $event->product;

        $target = $product->owner?->email ?? config('app.admin_email');

        Mail::to($target)->send(new ProductLowStockNotification($product));
    }
}
