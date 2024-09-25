<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletLog extends Model
{
    use HasFactory;

    protected $fillable = [
        "wallet_id",
        "userid",
        "previous_balance",
        "previous_blocked_balance",
        "amount",
        "log_rec",
        "new_balance",
        "new_blocked_balance",
        "transaction_type",
        "reference",
        "narration",
        "status",
    ];
}
