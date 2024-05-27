<?php

declare(strict_types=1);

namespace Tests\Unit\Security\Authentication\Domain;

use Tests\TestCase;
use Xp\StockManager\Security\Authentication\Domain\Exception\InvalidJWTException;
use Xp\StockManager\Security\Authentication\Domain\JWT;
use Xp\StockManager\Security\Authentication\Domain\ValueObject\Header;
use Xp\StockManager\Security\Authentication\Domain\ValueObject\Payload;
use Xp\StockManager\Shared\Domain\Utils\Strings;

final class JWTTest extends TestCase
{
    public function test_jwt_creation()
    {
        $now = time();

        $jwt = new JWT(
            new Header(),
            new Payload([
                'iat' => $now,
                'hello' => 'world',
            ])
        );

        $this->assertEquals('HS256', $jwt->header()->alg());
        $this->assertEquals($now, $jwt->payload()['iat']);
        $this->assertEquals('', $jwt->signature());
    }

    public function test_jwt_signatures()
    {
        $jwt = new JWT(
            new Header(),
            new Payload([
                'iat' => '1665409277',
                'hello' => 'world',
            ])
        );

        $jwt->sign('5ecr3tKeY');

        $this->assertEquals('4fae3d5314b984327de35f8ddf7cd2f21811c2dfabb5962ebf9d92855efa1b35', $jwt->signature());

        $jwt = new JWT(
            new Header('RS256'),
            new Payload([
                'iat' => '1665409277',
                'hello' => 'world',
            ])
        );

        $jwt->sign(openssl_pkey_get_private(
            file_get_contents(base_path('tests/.stubs/jwt/private.pem')),
            'secret'
        ));

        $this->assertEquals('HJ4cEXFe2B9L1MmwMp_0YvNUNPwN4zpCFMf-CLiIdpnwXdGvqc8nK3Cc7H3KJxCn-PPaxA8BOhbk4IsJW0k0wmMyA0ZT7Tn9pBCoLVZuAzUJxWD5KGJo0M3WhN_LWmF1S7bd1I-UYVRt5aO_-LzDNxTjORbN5rIJoCDKcm4LqQ8', $jwt->signature());
    }

    public function test_jwt_expansion(): void
    {
        $jwtString = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNjY1NDA5Mjc3IiwiaGVsbG8iOiJ3b3JsZCJ9.4fae3d5314b984327de35f8ddf7cd2f21811c2dfabb5962ebf9d92855efa1b35';

        $jwt = JWT::expand($jwtString);

        $this->assertEquals('HS256', $jwt->header()->alg());
        $this->assertEquals('1665409277', $jwt->payload()['iat']);
    }

    public function test_jwt_expansion_throws_exception_on_invalid_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('<eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNjY1NDA5Mjc3IiwiaGVsbG8iOiJ3b3JsZCJ9> is not a valid JWT token.');

        JWT::expand('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNjY1NDA5Mjc3IiwiaGVsbG8iOiJ3b3JsZCJ9');
    }

    public function test_jwt_verification_throws_exception_when_faked_payload(): void
    {
        $fakedPayload = Strings::urlSafeB64Encode(json_encode(['iat' => time(), 'hello' => 'everyone']));

        $jwt = JWT::expand(sprintf('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.%s.4fae3d5314b984327de35f8ddf7cd2f21811c2dfabb5962ebf9d92855efa1b35', $fakedPayload));

        $this->expectException(InvalidJWTException::class);
        $this->expectExceptionMessage('Invalid token signature');

        $jwt->verify('5ecr3tKeY');
    }

    public function test_jwt_expiration(): void
    {
        $jwt = JWT::expand('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpYXQiOiIxNjY1NDA5Mjc3IiwiaGVsbG8iOiJ3b3JsZCJ9.4fae3d5314b984327de35f8ddf7cd2f21811c2dfabb5962ebf9d92855efa1b35');

        $this->assertEquals(1665409577, $jwt->expires());
    }
}
