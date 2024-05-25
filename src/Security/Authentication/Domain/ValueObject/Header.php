<?php

declare(strict_types=1);

namespace Xp\StockManager\Security\Authentication\Domain\ValueObject;

use JsonSerializable;
use Stringable;
use Webmozart\Assert\Assert;
use Xp\StockManager\Shared\Domain\Utils\Strings;

final class Header implements JsonSerializable, Stringable
{
    public const SUPPORTS_ALGS = [
        'HS256' => ['hash_hmac', 'SHA256'],
        'RS256' => ['openssl', 'SHA256'],
    ];

    public const TYP_JWT = 'JWT';

    public function __construct(
        private readonly string $alg = 'HS256',
        private readonly string $typ = self::TYP_JWT,
    ) {
        Assert::inArray($alg, array_keys(self::SUPPORTS_ALGS));
    }

    public function alg(): string
    {
        return $this->alg;
    }

    public function typ(): string
    {
        return $this->typ;
    }

    public function jsonSerialize(): array
    {
        return [
            'alg' => $this->alg,
            'typ' => $this->typ,
        ];
    }

    public function __toString(): string
    {
        return Strings::urlSafeB64Encode(json_encode($this));
    }
}
