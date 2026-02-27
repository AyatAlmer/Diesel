<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{
    public function create(array $data)
    {
        return Order::create($data);
    }

    public function withRelations($order)
    {
        return $order->load('product', 'invoice');
    }
}
