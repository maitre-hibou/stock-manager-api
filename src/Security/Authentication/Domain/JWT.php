<?php

declare(strict_types=1);

namespace Xp\StockManager\Security\Authentication\Domain;

use Stringable;
use Xp\StockManager\Security\Authentication\Domain\Exception\InvalidJWTException;
use Xp\StockManager\Security\Authentication\Domain\ValueObject\Header;
use Xp\StockManager\Security\Authentication\Domain\ValueObject\Payload;
use Xp\StockManager\Shared\Domain\Utils\Strings;

class JWT implements Stringable
{
    public const int DEFAULT_TTL = 300;

    public function __construct(
        private readonly Header $header,
        private readonly Payload $payload,
        private string $signature = '',
    ) {
    }

    public function __toString(): string
    {
        return sprintf('%s.%s.%s', (string) $this->header(), (string) $this->payload(), $this->signature);
    }

    public function header(): Header
    {
        return $this->header;
    }

    public function payload(): Payload
    {
        return $this->payload;
    }

    public function signature(): string
    {
        return $this->signature;
    }

    public static function expand(string $jwtString): self
    {
        $tokenData = explode('.', $jwtString);

        if (3 !== count($tokenData)) {
            throw new \InvalidArgumentException(sprintf('<%s> is not a valid JWT token.', $jwtString));
        }

        $header = json_decode(Strings::urlsafeB64Decode($tokenData[0]), true);

        $payload = json_decode(Strings::urlsafeB64Decode($tokenData[1]), true);

        return new self(
            new Header($header['alg'], $header['typ']),
            new Payload($payload),
            $tokenData[2]
        );
    }

    public function sign(string|\OpenSSLAsymmetricKey|\OpenSSLCertificate $key): void
    {
        $imprint = sprintf('%s.%s', Strings::urlSafeB64Encode(json_encode($this->header())), Strings::urlSafeB64Encode(json_encode($this->payload())));

        list($function, $algorithm) = Header::SUPPORTS_ALGS[$this->header()->alg()];

        switch ($function) {
            case 'hash_hmac':
                if (false === is_string($key)) {
                    throw new \InvalidArgumentException('Key must be a string when using hmac algorithm');
                }
                $this->signature = hash_hmac($algorithm, $imprint, $key);
                break;
            case 'openssl':
                $signature = '';
                if (false === openssl_sign($imprint, $signature, $key, $algorithm)) {
                    throw new \RuntimeException('OpenSSL unable to sign token.');
                }
                $this->signature = Strings::urlSafeB64Encode($signature);
                break;
            default:
                throw new \BadMethodCallException(sprintf('"%s" algorithm is not supported.', $algorithm));
        }
    }

    public function verify(string|\OpenSSLCertificate|\OpenSSLAsymmetricKey $key): void
    {
        $imprint = sprintf('%s.%s', Strings::urlSafeB64Encode(json_encode($this->header())), Strings::urlSafeB64Encode(json_encode($this->payload())));

        list($function, $algorithm) = Header::SUPPORTS_ALGS[$this->header()->alg()];

        switch ($function) {
            case 'hash_hmac':
                $hash = hash_hmac($algorithm, $imprint, $key);

                if ($hash !== $this->signature()) {
                    throw (new InvalidJWTException('Invalid token signature'))->setToken($this);
                }
                break;
            case 'openssl':
                $success = \openssl_verify($imprint, Strings::urlsafeB64Decode($this->signature()), $key, $algorithm);
                if (1 !== $success) {
                    throw (new InvalidJWTException('Invalid token signature'))->setToken($this);
                }
                break;
            default:
                throw (new InvalidJWTException('Unsupported algorithm used'))->setToken($this);
        }

        if (false === isset($this->payload()['iat'])) {
            throw (new InvalidJWTException('Missing required "iat" field in payload'))->setToken($this);
        }
    }

    public function expires(int $ttl = self::DEFAULT_TTL): int
    {
        if (false === isset($this->payload()['iat'])) {
            throw (new InvalidJWTException('Missing required "iat" field in payload'))->setToken($this);
        }

        return ((int) $this->payload()['iat']) + $ttl;
    }
}
