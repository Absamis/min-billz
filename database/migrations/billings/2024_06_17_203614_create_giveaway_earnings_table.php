<?php

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
        Schema::create('giveaway_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("userid");
            $table->string("code");
            $table->unsignedBigInteger("giveaway_id");
            $table->string("status");
            $table->timestamps();
            $table->foreign("userid")->references("id")->on("users")->restrictOnDelete();
            $table->foreign("giveaway_id")->references("id")->on("giveaways")->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giveaway_earnings');
    }
};
