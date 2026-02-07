<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;

class PayPalGateway implements PaymentGatewayInterface
{
    /**
     * Test emails for PayPal simulation:
     * - success@test.com → Success
     * - declined@test.com → Declined
     * - error@test.com → Error
     */
    private const SUCCESS_EMAILS = ['success@test.com', 'approved@test.com'];
    private const DECLINED_EMAILS = ['declined@test.com', 'reject@test.com'];
    private const ERROR_EMAILS = ['error@test.com'];

    public function process(float $amount, array $details = []): PaymentResult
    {
        $email = strtolower($details['email'] ?? '');
        $transactionId = 'PP_' . uniqid() . '_' . time();

        if (in_array($email, self::ERROR_EMAILS)) {
            return PaymentResult::failed(
                $transactionId,
                'PayPal processing error',
                ['error_code' => 'PAYPAL_ERROR']
            );
        }

        if (in_array($email, self::DECLINED_EMAILS)) {
            return PaymentResult::failed(
                $transactionId,
                'PayPal payment declined',
                ['error_code' => 'PAYPAL_DECLINED']
            );
        }

        if (in_array($email, self::SUCCESS_EMAILS) || str_ends_with($email, '@test.com')) {
            return PaymentResult::success(
                $transactionId,
                'PayPal payment approved',
                [
                    'payer_id' => 'PAYER_' . strtoupper(bin2hex(random_bytes(4))),
                    'payer_email' => $email,
                ]
            );
        }

        // Any other email - simulate success for demo
        return PaymentResult::success(
            $transactionId,
            'PayPal payment approved',
            ['payer_email' => $email]
        );
    }

    public function getName(): string
    {
        return 'paypal';
    }

    public function isConfigured(): bool
    {
        // In real app: check config('services.paypal.client_id')
        return true;
    }
}
