<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\DTOs\PaymentResult;
use App\Support\TestCards;

class StripeGateway implements PaymentGatewayInterface
{
    /**
     * note:-
     * Uses the same test card numbers as CreditCard gateway
     * In real implementation, will use Stripe SDK
     */
    public function process(float $amount, array $details = []): PaymentResult
    {
        $cardNumber = $details['card_number'] ?? '';
        $token = $details['token'] ?? null;

        // Stripe uses prefixed transaction IDs
        $transactionId = 'ch_' . bin2hex(random_bytes(12));

        // If token provided, simulate token-based payment
        if ($token) {
            if (str_starts_with($token, 'tok_fail')) {
                return PaymentResult::failed(
                    $transactionId,
                    'Stripe payment failed',
                    ['error' => 'card_declined']
                );
            }

            return PaymentResult::success(
                $transactionId,
                'Stripe payment successful',
                [
                    'payment_intent' => 'pi_' . bin2hex(random_bytes(12)),
                    'receipt_url' => 'https://pay.stripe.com/receipts/' . $transactionId,
                ]
            );
        }

        // Card number based simulation
        if (TestCards::isError($cardNumber)) {
            return PaymentResult::failed(
                $transactionId,
                TestCards::getErrorReason($cardNumber),
                ['stripe_error' => 'processing_error']
            );
        }

        if (TestCards::isDeclined($cardNumber)) {
            return PaymentResult::failed(
                $transactionId,
                TestCards::getDeclineReason($cardNumber),
                ['stripe_error' => 'card_declined']
            );
        }

        if (TestCards::isSuccess($cardNumber)) {
            return PaymentResult::success(
                $transactionId,
                'Stripe payment successful',
                [
                    'payment_intent' => 'pi_' . bin2hex(random_bytes(12)),
                    'receipt_url' => 'https://pay.stripe.com/receipts/' . $transactionId,
                ]
            );
        }

        return PaymentResult::failed(
            $transactionId,
            'Invalid card',
            ['stripe_error' => 'invalid_card']
        );
    }

    public function getName(): string
    {
        return 'stripe';
    }

    public function isConfigured(): bool
    {
        // In a real app: check config('services.stripe.secret')
        return true;
    }
}
