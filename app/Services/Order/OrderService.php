<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Models\User;
use App\Repositories\Order\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository
    ) {}

    public function createOrder(User $user, array $items): Order
    {
        $order = $this->orderRepository->create($user->id, [
            'status' => OrderStatus::Pending,
        ]);

        $this->orderRepository->addItems($order, $items);

        return $order->load('items');
    }

    public function updateOrder(Order $order, array $data): Order
    {
        if (isset($data['items'])) {
            $this->orderRepository->updateItems($order, $data['items']);
            unset($data['items']);
        }

        if (!empty($data)) {
            $this->orderRepository->update($order, $data);
        }

        return $order->fresh(['items']);
    }

    public function deleteOrder(Order $order): bool
    {
        return $this->orderRepository->delete($order);
    }

    public function getOrderById(int $id): ?Order
    {
        return $this->orderRepository->findById($id);
    }

    public function getUserOrders(User $user, ?string $status = null): LengthAwarePaginator
    {
        $orderStatus = $status ? OrderStatus::tryFrom($status) : null;

        return $this->orderRepository->findByUser($user->id, $orderStatus);
    }

    public function updateStatus(Order $order, OrderStatus $status): Order
    {
        if ($order->status === OrderStatus::Cancelled) {
            throw OrderException::cannotModifyCancelled();
        }

        return $this->orderRepository->update($order, ['status' => $status]);
    }
}
