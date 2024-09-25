<?php

namespace App\Http\Resources\Apis;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonnifyApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request):array
    {
        $dt = [
            "requestSuccessful" => $this->requestSuccessful,
            "responseMessage" => $this->responseMessage,
            "responseCode" => $this->responseCode,
            "responseBody" => $this->responseBody
        ];
        return $dt;
    }
}
