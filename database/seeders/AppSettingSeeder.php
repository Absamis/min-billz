<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        AppSetting::updateOrCreate(["id" => 1], [
            "app_name" => config("app.name"),
            "referral_bonus" => 0,
            "referral_percentage" => 0,
            "min_wallet_funding_amount" => 100,
            "max_wallet_funding_amount" => 1000000,
            "app_currency" => "NGN",
            "min_transaction_limit" => 100,
            "max_transaction_limit" => 50000,
            "giveaway_expires_in" => "3600"
        ]);
    }
}
