<?php

namespace App\DTOs;

readonly class PaymentResult
{
    public function __construct(
        public bool $success,
        public string $transactionId,
        public string $message,
        public array $rawResponse = []
    ) {}

    public static function success(string $transactionId, string $message = 'Payment successful', array $rawResponse = []): self
    {
        return new self(true, $transactionId, $message, $rawResponse);
    }

    public static function failed(string $transactionId, string $message = 'Payment failed', array $rawResponse = []): self
    {
        return new self(false, $transactionId, $message, $rawResponse);
    }
}
