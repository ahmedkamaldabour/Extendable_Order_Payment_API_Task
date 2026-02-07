<?php

namespace App\Exceptions;

class OrderException extends BusinessException
{
    protected function getErrorKey(): string
    {
        return 'order';
    }

    public static function cannotModifyCancelled(): self
    {
        return new self('Cancelled orders cannot be modified', 422);
    }

    public static function cannotDeleteWithPayments(): self
    {
        return new self('Cannot delete order with associated payments', 422);
    }

    public static function cannotCancelWithPayment(): self
    {
        return new self('Cannot cancel order with successful payment', 422);
    }
}
