<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        "app_name",
        "referral_bonus",
        "referral_percentage",
        "status",
        "min_transaction_limit",
        "max_transaction_limit",
        "min_wallet_funding_amount",
        "max_wallet_funding_amount"
    ];
}

