<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use App\Support\TestCards;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function process(float $amount, array $details = []): PaymentResult
    {
        $cardNumber = $details['card_number'] ?? '';
        $transactionId = 'CC_' . uniqid() . '_' . time();

        // Simulate based on test card numbers

        if (TestCards::isError($cardNumber)) {
            return PaymentResult::failed(
                $transactionId,
                TestCards::getErrorReason($cardNumber),
                ['error_code' => 'PROCESSING_ERROR']
            );
        }

        if (TestCards::isDeclined($cardNumber)) {
            return PaymentResult::failed(
                $transactionId,
                TestCards::getDeclineReason($cardNumber),
                ['error_code' => 'CARD_DECLINED']
            );
        }

        if (TestCards::isSuccess($cardNumber)) {
            return PaymentResult::success(
                $transactionId,
                'Payment approved',
                [
                    'authorization_code' => strtoupper(bin2hex(random_bytes(4))),
                    'card_last_four' => substr($cardNumber, -4),
                ]
            );
        }

        // Unknown card - treat as declined
        return PaymentResult::failed(
            $transactionId,
            'Invalid card number',
            ['error_code' => 'INVALID_CARD']
        );
    }

    public function getName(): string
    {
        return 'credit_card';
    }

    public function isConfigured(): bool
    {
        return true;
    }
}
