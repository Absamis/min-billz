<?php

namespace App\Models\Billings\VTU;

use App\Traits\Scopes\StatusScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBundleService extends Model
{
    use HasFactory, StatusScope;
    protected $fillable = [
        "name",
        "code",
        "vendor",
        "remarks",
        "image",
        "status"
    ];

    public function plans(){
        return $this->hasMany(DataBundlePlan::class, "service_id");
    }
}
