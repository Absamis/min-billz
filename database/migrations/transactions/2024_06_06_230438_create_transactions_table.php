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
        Schema::create('transactions', function (Blueprint $table) {
            $table->string("id")->primary();
            $table->unsignedBigInteger("userid");
            $table->double("amount");
            $table->double("charges")->default(0);
            $table->string("currency");
            $table->string("transaction_type");
            $table->unsignedBigInteger("payment_method_id")->nullable();
            $table->string("payment_gateway")->nullable();
            $table->string("payment_channel")->nullable();
            $table->text("payment_reference")->nullable();
            $table->timestamp("transaction_date");
            $table->longText("narration")->nullable();
            $table->longText("data")->nullable();
            $table->string("status");
            $table->timestamps();
            $table->foreign("payment_method_id")->references("id")->on("payment_methods")->restrictOnDelete();
            $table->foreign("userid")->references("id")->on("users")->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
