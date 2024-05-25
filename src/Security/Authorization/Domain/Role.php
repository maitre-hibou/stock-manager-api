<?php

declare(strict_types=1);

namespace Xp\StockManager\Security\Authorization\Domain;

enum Role: string
{
    case ADMIN = 'admin';
    case USER = 'user';
}
