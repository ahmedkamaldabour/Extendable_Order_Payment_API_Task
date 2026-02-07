<?php

namespace App\Services\Payment;

use App\Enums\PaymentStatus;
use App\Exceptions\PaymentException;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\Payment\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class PaymentService
{
    public function __construct(
        private readonly PaymentRepositoryInterface $paymentRepository,
        private readonly GatewayManager $gatewayManager
    ) {}

    public function processPayment(Order $order, string $method, array $details = []): Payment
    {
        // Get the gateway instance
        $gateway = $this->gatewayManager->gateway($method);

        $result = $gateway->process($order->total, $details);

        $payment = $this->paymentRepository->create([
            'order_id' => $order->id,
            'payment_id' => $result->transactionId,
            'status' => $result->success ? PaymentStatus::Successful : PaymentStatus::Failed,
            'method' => $method,
            'amount' => $order->total,
            'gateway_response' => $result->rawResponse,
        ]);

        if (!$result->success) {
            throw PaymentException::processingFailed($result->message);
        }

        return $payment;
    }

    public function getOrderPayments(Order $order): Collection
    {
        return $this->paymentRepository->findByOrder($order);
    }

    public function getPaymentById(int $id): ?Payment
    {
        return $this->paymentRepository->findById($id);
    }

    public function availableMethods(): array
    {
        return $this->gatewayManager->availableGateways();
    }
}
