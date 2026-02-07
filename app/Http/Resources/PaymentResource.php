<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->payment_id,
            'order_id' => $this->order_id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'method' => $this->method,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
        ];
    }
}
