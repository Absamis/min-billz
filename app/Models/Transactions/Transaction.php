<?php

namespace App\Models\Transactions;

use App\Enums\TransactionEnums;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        "id",
        "userid",
        "amount",
        "charges",
        "currency",
        "transaction_type",
        "payment_method_id",
        "payment_gateway",
        "payment_channel",
        "payment_reference",
        "transaction_date",
        "narration",
        "data",
        "status"
    ];

    protected $hidden = [
        "data",
    ];

    public function user(){
        return $this->belongsTo(User::class, "userid");
    }

    public function payment_method(){
        return $this->belongsTo(PaymentMethod::class, "payment_method_id");
    }

    public function displayAmount():Attribute
    {
        return Attribute::make(
            get: function(){
                return TransactionEnums::currenciesSymbol[$this->currency].number_format($this->amount,2);
            }
        );
    }
}
