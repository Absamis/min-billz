<?php

namespace App\Models;

use App\Traits\Scopes\StatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralBonus extends Model
{
    use HasFactory, StatusScope;
    protected $fillable = [
        "userid",
        "referee_id",
        "entitled_bonus",
        "bonus_earned",
        "percentage_charge",
        "status",
    ];

    public function user(){
        return $this->belongsTo(User::class, "userid");
    }

    public function referee(){
        return $this->belongsTo(User::class, "referee_id");
    }
}
