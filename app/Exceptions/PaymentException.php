<?php

namespace App\Exceptions;

class PaymentException extends BusinessException
{
    protected function getErrorKey(): string
    {
        return 'payment';
    }

    public static function invalidGateway(string $method): self
    {
        $available = implode(', ', config('payment.gateways', []));
        return new self("Invalid payment method: {$method}. Available: {$available}", 422);
    }

    public static function gatewayNotConfigured(string $method): self
    {
        return new self("Payment gateway '{$method}' is not properly configured", 500);
    }

    public static function orderNotConfirmed(): self
    {
        return new self('Payment can only be processed for confirmed orders', 422);
    }

    public static function processingFailed(string $reason): self
    {
        return new self("Payment processing failed: {$reason}", 422);
    }
}
