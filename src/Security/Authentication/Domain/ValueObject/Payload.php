<?php

declare(strict_types=1);

namespace Xp\StockManager\Security\Authentication\Domain\ValueObject;

use ArrayAccess;
use JsonSerializable;
use Stringable;
use Xp\StockManager\Shared\Domain\Utils\Strings;

final class Payload implements ArrayAccess, JsonSerializable, Stringable
{
    public function __construct(
        private array $data
    ) {
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString(): string
    {
        return Strings::urlSafeB64Encode(json_encode($this));
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data['offset'] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
