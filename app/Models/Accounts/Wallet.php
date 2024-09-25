<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = [
        "userid",
        "balance",
        "blocked_balance",
        "transaction_limit",
        "remarks",
    ];

    public function accounts(){
        return $this->hasMany(WalletAccount::class, "wallet_id");
    }
}
