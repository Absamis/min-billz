<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingTransaction extends Model
{
    use HasFactory;

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        "id",
        "userid",
        "amount",
        "vendor_amount",
        "charges",
        "currency",
        "transaction_type",
        "vendor",
        "service_type",
        "reference",
        "service_name",
        "service_id",
        "recipient",
        "payment_method",
        "narration",
        "data",
        "status",
    ];

    protected $hidden = [
        "data",
        "vendor_amount",
    ];

    protected function casts(): array
    {
        return [
            "data" => "encrypted:object"
        ];
    }
}
