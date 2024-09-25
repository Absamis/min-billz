<?php

namespace App\Http\Resources\Billings\VTU;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataBundleServiceResource extends JsonResource
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
            "code" => $this->code,
            "remarks" => $this->remarks,
            "status" => $this->status,
            "image" => $this->image,
            "plans" => DataBundlePlanResource::collection($this->plans()->active()->get())
        ];
    }
}
