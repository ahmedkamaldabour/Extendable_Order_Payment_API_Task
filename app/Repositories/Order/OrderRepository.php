<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?Order
    {
        return Order::with('items')->find($id);
    }

    public function findByUser(int $userId, ?OrderStatus $status = null): LengthAwarePaginator
    {
        $query = Order::with('items')
            ->where('user_id', $userId)
            ->latest();

        if ($status !== null) {
            $query->where('status', $status->value);
        }

        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    public function create(int $userId, array $data): Order
    {
        return Order::create([
            'user_id' => $userId,
            'total' => $data['total'] ?? 0,
            'status' => $data['status'] ?? OrderStatus::Pending,
        ]);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order->fresh();
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function addItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $order->items()->create([
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'subtotal' => $item['quantity'] * $item['price'],
            ]);
        }

        $order->updateTotal();
    }

    public function updateItems(Order $order, array $items): void
    {
        $order->items()->delete();
        $this->addItems($order, $items);
    }
}
