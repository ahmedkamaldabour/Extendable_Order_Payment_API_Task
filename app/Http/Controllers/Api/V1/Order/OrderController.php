<?php

namespace App\Http\Controllers\Api\V1\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Requests\Order\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\Order\OrderService;
use App\Traits\APIS\ApiResponseTrait;
use App\Enums\OrderStatus;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly OrderService $orderService) {}

    public function index()
    {
        $status = request()->query('status');
        $orders = $this->orderService->getUserOrders(auth()->user(), $status);

        return $this->apiResponse(
            code: 200,
            message: 'Orders retrieved successfully',
            data: OrderResource::collection($orders)->response()->getData(true)
        );
    }

    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderService->createOrder(
            auth()->user(),
            $request->validated()['items']
        );

        return $this->apiResponse(
            code: 201,
            message: 'Order created successfully',
            data: new OrderResource($order)
        );
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load('items');

        return $this->apiResponse(
            code: 200,
            message: 'Order retrieved successfully',
            data: new OrderResource($order)
        );
    }

    public function update(UpdateOrderRequest $request, Order $order)
    {
        $this->authorize('update', $order);

        $order = $this->orderService->updateOrder($order, $request->validated());

        return $this->apiResponse(
            code: 200,
            message: 'Order updated successfully',
            data: new OrderResource($order)
        );
    }

    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);

        $this->orderService->deleteOrder($order);

        return $this->apiResponse(
            code: 200,
            message: 'Order deleted successfully'
        );
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->authorize('update', $order);

        $status = OrderStatus::from($request->validated()['status']);
        $order = $this->orderService->updateStatus($order, $status);

        return $this->apiResponse(
            code: 200,
            message: 'Order status updated successfully',
            data: new OrderResource($order)
        );
    }
}
