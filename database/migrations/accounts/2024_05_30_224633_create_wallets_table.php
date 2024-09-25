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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("userid");
            $table->double("balance")->default(0);
            $table->double("blocked_balance")->default(0);
            $table->double("transaction_limit");
            $table->string("remarks")->nullable();
            $table->integer("status")->default(AppEnums::active);
            $table->timestamps();
            $table->foreign("userid")->references("id")->on("users")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
