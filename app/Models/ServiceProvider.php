<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
        "code",
        "category",
        "token",
        "description",
        "remarks",
        "image",
        "status"
    ];
}
