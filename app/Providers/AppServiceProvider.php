<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Xp\StockManager\Security\Authorization\Domain\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            if (Role::ADMIN->value === $user->role) {
                return true;
            }

            return null;
        });

        Gate::define('edit_product', function (User $user, Product $product) {
            return $product->owner->id === $user->id;
        });

        Gate::define('delete_product', function (User $user, Product $product) {
            return $product->owner->id === $user->id;
        });
    }
}
