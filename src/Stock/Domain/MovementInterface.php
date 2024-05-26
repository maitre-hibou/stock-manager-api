<?php

declare(strict_types=1);

namespace Xp\StockManager\Stock\Domain;

interface MovementInterface
{
    public const DIRECTION_IN = 'in';
    public const DIRECTION_OUT = 'out';
}
