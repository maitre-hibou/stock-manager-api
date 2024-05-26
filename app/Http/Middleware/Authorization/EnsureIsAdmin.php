<?php

declare(strict_types=1);

namespace App\Http\Middleware\Authorization;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Xp\StockManager\Security\Authorization\Domain\Role;

final class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (null === $request->user()) {
            throw new HttpResponseException(response()->json([
                'message' => 'You must be authenticated to access this resource.',
            ], \Illuminate\Http\Response::HTTP_UNAUTHORIZED));
        }

        if (Role::ADMIN->value !== $request->user()->role) {
            throw new HttpResponseException(response()->json([
                'message' => 'Resource restricted to admin role.',
            ], \Illuminate\Http\Response::HTTP_UNAUTHORIZED));
        }

        return $next($request);
    }
}
