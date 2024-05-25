<?php

declare(strict_types=1);

namespace Xp\StockManager\Security\Authentication\Domain\Exception;

use Xp\StockManager\Security\Authentication\Domain\JWT;

final class InvalidJWTException extends \DomainException
{
    private JWT $token;

    public function __construct(string $message = '')
    {
        parent::__construct($message);
    }

    public function getToken(): JWT
    {
        return $this->token;
    }

    public function setToken(JWT $token): self
    {
        $this->token = $token;

        return $this;
    }
}
