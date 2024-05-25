<?php

namespace App\Http\Middleware\Auth;

use App\Models\User;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Xp\StockManager\Security\Authentication\Domain\Exception\InvalidJWTException;
use Xp\StockManager\Security\Authentication\Domain\JWT;

final class EnsureJWTAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->headers->has('Authorization') || !preg_match('/^Bearer .+$/', $request->headers->get('Authorization'))) {
            throw new HttpResponseException(response()->json([
                'message' => 'You must be authenticated to access this resource.',
            ], \Illuminate\Http\Response::HTTP_UNAUTHORIZED));
        }

        $jwt = JWT::expand(str_replace('Bearer ', '', $request->headers->get('Authorization')));

        try {
            $jwt->verify(config('auth.jwt_encrypt_key'));

            if (!$jwt->isStillValid()) {
                throw new InvalidJWTException('This token is no longer valid');
            }
        } catch (InvalidJWTException $e) {
            throw new HttpResponseException(response()->json([
                'message' => $e->getMessage(),
            ], \Illuminate\Http\Response::HTTP_UNAUTHORIZED));
        }

        /** @var User $user */
        if (null === ($user = User::where('email', $jwt->payload()['email'])->first())) {
            throw new HttpResponseException(response()->json(['message' => 'This user does not exists.'], \Illuminate\Http\Response::HTTP_UNAUTHORIZED));
        }

        Auth::onceUsingId($user->id);

        return $next($request);
    }
}
