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
        Schema::create('wallet_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("wallet_id")->nullable();
            $table->unsignedBigInteger("userid");
            $table->string("account_name");
            $table->string("account_number");
            $table->string("bank_name");
            $table->string("bank_code");
            $table->string("provider");
            $table->integer("status")->default(AppEnums::active);
            $table->timestamps();
            $table->foreign("userid")->references("id")->on("users")->cascadeOnDelete();
            $table->foreign("wallet_id")->references("id")->on("wallets")->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_accounts');
    }
};
