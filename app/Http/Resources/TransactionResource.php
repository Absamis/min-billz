<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            "amount" => $this->amount,
            "currency" => $this->currency,
            "transaction_type" => $this->transaction_type,
            "payment_method" => $this->payment_method,
            "payment_gateway" => $this->payment_gateway,
            "payment_reference" => $this->payment_reference,
            "transaction_date" => $this->transaction_date,
            "narration" => $this->narration,
            "status" => $this->status,
        ];
    }
}
