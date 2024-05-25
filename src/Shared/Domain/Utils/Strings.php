<?php

declare(strict_types=1);

namespace Xp\StockManager\Shared\Domain\Utils;

final class Strings
{
    public static function urlSafeB64Encode(string $value): string
    {
        return str_replace('=', '', strtr(base64_encode($value), '+/', '-_'));
    }

    public static function urlsafeB64Decode(string $value): string
    {
        $remainder = \strlen($value) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $value .= \str_repeat('=', $padlen);
        }

        return \base64_decode(\strtr($value, '-_', '+/'), true);
    }
}
