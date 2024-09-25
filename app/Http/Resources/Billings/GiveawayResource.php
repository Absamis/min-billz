<?php

namespace App\Http\Resources\Billings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiveawayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "userid" => $this->userid,
            "code" => $this->code,
            "bill_type" => $this->bill_type,
            "service_id" => $this->service_id,
            "service_name" => $this->service_name,
            "service_type" => $this->service_type,
            "unit_price" => $this->unit_price,
            "quantity_bought" => $this->quantity_bought,
            "quantity_claimed" => $this->quantity_claimed,
            "quantity_left" => $this->quantity_bought - $this->quantity_claimed,
            "amount_spent" => $this->quantity_claimed * $this->unit_price,
            "total_amount" => $this->total_amount,
            "status" => $this->status,
            "expired_in" => $this->expires_in,
        ];
    }
}
