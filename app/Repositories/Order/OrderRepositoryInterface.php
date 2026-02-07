<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;

    public function findByUser(int $userId, ?OrderStatus $status = null): LengthAwarePaginator;

    public function create(int $userId, array $data): Order;

    public function update(Order $order, array $data): Order;

    public function delete(Order $order): bool;

    public function addItems(Order $order, array $items): void;

    public function updateItems(Order $order, array $items): void;
}
