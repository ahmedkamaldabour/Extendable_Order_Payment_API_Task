<?php

namespace App\Repositories\Payment;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function findById(int $id): ?Payment
    {
        return Payment::find($id);
    }

    public function findByPaymentId(string $paymentId): ?Payment
    {
        return Payment::where('payment_id', $paymentId)->first();
    }

    public function findByOrder(Order $order): Collection
    {
        return Payment::forOrder($order->id)->latest()->get();
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function updateStatus(Payment $payment, PaymentStatus $status): Payment
    {
        $payment->update(['status' => $status]);
        return $payment->fresh();
    }
}
