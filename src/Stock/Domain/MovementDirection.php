<?php

declare(strict_types=1);

namespace Xp\StockManager\Stock\Domain;

enum MovementDirection: string
{
    case IN = MovementInterface::DIRECTION_IN;
    case OUT = MovementInterface::DIRECTION_OUT;
}
