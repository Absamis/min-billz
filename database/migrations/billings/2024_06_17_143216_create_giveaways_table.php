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
        Schema::create('giveaways', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("userid");
            $table->string("code");
            $table->string("bill_type");
            $table->string("service_id");
            $table->string("service_name");
            $table->string("service_type");
            $table->double("unit_price");
            $table->integer("quantity_bought");
            $table->integer("quantity_claimed")->default(0);
            $table->double("total_amount");
            $table->string("status");
            $table->string("expired_in");
            $table->timestamps();
            $table->foreign("userid")->references("id")->on("users")->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giveaways');
    }
};
