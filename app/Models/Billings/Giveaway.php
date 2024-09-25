<?php

namespace App\Models\Billings;

use App\Enums\BillingEnums;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    use HasFactory;
    protected $fillable = [
        "userid",
        "code",
        "bill_type",
        "service_id",
        "service_name",
        "service_type",
        "unit_price",
        "quantity_bought",
        "quantity_claimed",
        "total_amount",
        "status",
        "expired_in",
    ];

    public function user(){
        return $this->belongsTo(User::class, "userid");
    }

    public function scopeActive(Builder $builder){
        return $builder->where("status", BillingEnums::activeGiveaway);
    }

    public function earnings(){
        return $this->hasMany(GiveawayEarning::class, "giveaway_id");
    }
}

