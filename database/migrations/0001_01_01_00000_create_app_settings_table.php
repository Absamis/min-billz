<?php

use App\Enums\AppEnums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string("app_name");
            $table->string("app_currency");
            $table->double("referral_bonus");
            $table->double("referral_percentage");
            $table->double("min_wallet_funding_amount");
            $table->double("max_wallet_funding_amount");
            $table->bigInteger("min_transaction_limit");
            $table->bigInteger("max_transaction_limit");
            $table->string("giveaway_expires_in");
            $table->integer("status")->default(AppEnums::active);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
