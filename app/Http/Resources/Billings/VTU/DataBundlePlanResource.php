<?php

namespace App\Http\Resources\Billings\VTU;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataBundlePlanResource extends JsonResource
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
            "name" => $this->name,
            "price" => $this->price,
            "service_name" => $this->service_name,
            "service_plan_id" => $this->service_plan_id,
            "remarks" => $this->remarks,
            "status" => $this->status,
        ];
    }
}
