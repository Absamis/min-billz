<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\AccountEnums;
use App\Models\Accounts\Wallet;
use App\Models\Accounts\WalletAccount;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        "phone_number",
        'email_verified_at',
        'password',
        "profile_photo",
        "pin",
        "user_tag",
        "referrer_id",
        "last_login",
        "last_login_location",
        "last_login_ip",
        "status",
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'pin' => 'hashed',
        ];
    }

    public function referrer(){
        return $this->belongsTo(User::class, "referrer_id");
    }

    public function profilePhoto():Attribute{
        return Attribute::make(
            get: function($value){
                return Storage::disk("upl")->url($value);
            }
        );
    }
    public function wallet(){
        return $this->hasOne(Wallet::class, "userid");
    }

    public function wallet_accounts(){
        return $this->hasMany(WalletAccount::class, "userid");
    }

    public function isActive():Attribute
    {
        return Attribute::make(
            get: function(){
                return $this->status == AccountEnums::verifiedAccount;
            }
        );
    }

    public function isUnverified():Attribute
    {
        return Attribute::make(
            get: function(){
                return $this->status == AccountEnums::unverifiedAccount;
            }
        );
    }
}
