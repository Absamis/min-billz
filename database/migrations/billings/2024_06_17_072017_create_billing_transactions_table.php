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
        Schema::create('billing_transactions', function (Blueprint $table) {
            $table->string("id")->primary();
            $table->unsignedBigInteger("userid");
            $table->double("amount");
            $table->double("vendor_amount");
            $table->double("charges")->default(0);
            $table->string("currency");
            $table->string("transaction_type");
            $table->string("vendor");
            $table->string("service_type");
            $table->string("reference")->nullable();
            $table->string("service_name")->nullable();
            $table->string("service_id");
            $table->string("recipient");
            $table->string("payment_method")->nullable();
            $table->longText("narration")->nullable();
            $table->longText("data")->nullable();
            $table->string("status");
            $table->timestamps();
            $table->foreign("userid")->references("id")->on("users")->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing_transactions');
    }
};
