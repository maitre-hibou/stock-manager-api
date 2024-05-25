<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Xp\StockManager\Security\Authentication\Domain\JWT;
use Xp\StockManager\Security\Authentication\Domain\ValueObject as AuthValueObjects;

final class LoginController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data sent', 'errors' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        if (RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($this->throttleKey($request));

            return response()->json(['message' => sprintf('Too many attempts. Try again in %d seconds.', $seconds)], Response::HTTP_BAD_REQUEST);
        }

        $userProvider = Auth::getProvider();
        $credentials = $request->only('email', 'password');

        /** @var User $user */
        if (null === ($user = $userProvider->retrieveByCredentials($credentials))) {
            RateLimiter::hit($this->throttleKey($request));

            return response()->json(['message' => sprintf('Unable to authenticate user with email %s', $request->get('email'))], Response::HTTP_NOT_ACCEPTABLE);
        }

        if (!$userProvider->validateCredentials($user, $credentials)) {
            return response()->json(['message' => sprintf('Unable to authenticate user with emssail %s', $request->get('email'))], Response::HTTP_NOT_ACCEPTABLE);
        }

        $jwt = $this->generateJWT($user->jwtSerialize());

        return response()->json(['access_token' => (string) $jwt, 'expires' => $jwt->expires()]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }

    private function generateJWT(array $data): JWT
    {
        $jwt = new JWT(
            new AuthValueObjects\Header(),
            new AuthValueObjects\Payload(array_merge([
                'iat' => time(),
            ], $data))
        );

        $jwt->sign(config('auth.jwt_encrypt_key'));

        return $jwt;
    }
}
