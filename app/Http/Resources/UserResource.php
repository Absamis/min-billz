<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            "phone_number" => $this->phone_number,
            "profile_photo" => $this->profile_photo,
            "user_tag" => $this->user_tag,
            "referrer_id" => $this->referrer,
            "last_login" => $this->last_login,
            "last_login_location" => $this->last_login_location,
            "last_login_ip" => $this->last_login_ip,
            "isPinCreated" => $this->pin ? true: false,
            "access_token" => $this->when($this->access_token, $this->access_token),
            "status" => $this->status,
            "wallet" => $this->wallet,
        ];
    }
}
