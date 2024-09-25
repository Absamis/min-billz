<?php

namespace App\Models\Billings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiveawayEarning extends Model
{
    use HasFactory;
    protected $fillable = [
        "userid",
        "code",
        "giveaway_id",
        "status"
    ];

    public function giveaway(){
        return $this->belongsTo(Giveaway::class, "giveaway_id");
    }
}
