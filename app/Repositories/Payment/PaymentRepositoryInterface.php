<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment;

    public function findByPaymentId(string $paymentId): ?Payment;

    public function findByOrder(Order $order): Collection;

    public function create(array $data): Payment;

    public function updateStatus(Payment $payment, PaymentStatus $status): Payment;
}
