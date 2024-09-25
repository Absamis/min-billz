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
        Schema::create('referral_bonuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("userid");
            $table->unsignedBigInteger("referee_id");
            $table->double("entitled_bonus");
            $table->double("bonus_earned");
            $table->double("percentage_charge");
            $table->integer("status")->default(AppEnums::active);
            $table->timestamps();
            $table->foreign("userid")->references("id")->on("users")->restrictOnDelete();
            $table->foreign("referee_id")->references("id")->on("users")->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_bonuses');
    }
};
