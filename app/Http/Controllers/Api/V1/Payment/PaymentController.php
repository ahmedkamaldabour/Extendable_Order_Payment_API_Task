<?php

namespace App\Http\Controllers\Api\V1\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function process(ProcessPaymentRequest $request, Order $order)
    {
        $this->authorize('update', $order);

        $payment = $this->paymentService->processPayment(
            $order,
            $request->validated()['method'],
            $request->validated()
        );

        return $this->apiResponse(
            code: 201,
            message: 'Payment processed successfully',
            data: new PaymentResource($payment)
        );
    }

    public function index(Order $order)
    {
        $this->authorize('view', $order);

        $payments = $this->paymentService->getOrderPayments($order);

        return $this->apiResponse(
            code: 200,
            message: 'Payments retrieved successfully',
            data: PaymentResource::collection($payments)
        );
    }

    public function methods()
    {
        return $this->apiResponse(
            code: 200,
            message: 'Available payment methods',
            data: $this->paymentService->availableMethods()
        );
    }
}
