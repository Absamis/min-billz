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
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("wallet_id");
            $table->unsignedBigInteger("userid");
            $table->double("previous_balance");
            $table->double("previous_blocked_balance");
            $table->double("amount");
            $table->double("new_balance");
            $table->double("new_blocked_balance");
            $table->string("transaction_type");
            $table->string("reference")->nullable();
            $table->string("narration")->nullable();
            $table->string("log_rec")->nullable();
            $table->integer("status")->default(AppEnums::active);
            $table->timestamps();
            $table->foreign("userid")->references("id")->on("users")->cascadeOnDelete();
            $table->foreign("wallet_id")->references("id")->on("wallets")->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};
