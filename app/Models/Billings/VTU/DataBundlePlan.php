<?php

namespace App\Models\Billings\VTU;

use App\Traits\Scopes\StatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBundlePlan extends Model
{
    use HasFactory, StatusScope;
    protected $fillable = [
        "name",
        "vendor_price",
        "price",
        "service_name",
        "service_id",
        "service_plan_id",
        "vendor",
        "remarks",
        "status",
    ];

    public function service(){
        return $this->belongsTo(DataBundleService::class, "service_id");
    }

}
