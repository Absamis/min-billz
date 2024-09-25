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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("code", 50);
            $table->string("category");
            $table->string("image")->nullable();
            $table->string("token")->nullable();
            $table->string("description")->nullable();
            $table->string("remarks")->nullable();
            $table->integer("status")->default(AppEnums::active);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
