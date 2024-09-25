<?php

namespace App\Models\Accounts;

use App\Traits\Scopes\StatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletAccount extends Model
{
    use HasFactory, StatusScope;
    protected $fillable = [
        "wallet_id",
        "userid",
        "account_name",
        "account_number",
        "bank_name",
        "bank_code",
        "provider",
        "status",
    ];
}
