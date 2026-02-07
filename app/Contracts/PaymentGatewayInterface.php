<?php

namespace App\Contracts;

use App\DTOs\PaymentResult;

interface PaymentGatewayInterface
{
    public function process(float $amount, array $details = []): PaymentResult;

    public function getName(): string;

    public function isConfigured(): bool;
}
