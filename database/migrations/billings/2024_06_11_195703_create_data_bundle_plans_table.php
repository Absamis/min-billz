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
        Schema::create('data_bundle_plans', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->double("vendor_price");
            $table->double("price");
            $table->string("service_name");
            $table->unsignedBigInteger("service_id")->nullable();
            $table->string("service_plan_id");
            $table->string("vendor");
            $table->string("remarks")->nullable();
            $table->integer("status")->default(AppEnums::active);
            $table->timestamps();
            $table->foreign("service_id")->references("id")->on("data_bundle_services")->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_bundle_plans');
    }
};
