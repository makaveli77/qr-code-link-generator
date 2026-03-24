<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("scans", function (Blueprint $table) {
            $table->id();
            $table->foreignId("link_id")->constrained()->onDelete("cascade");
            $table->foreignId("device_id")->constrained()->onDelete("cascade");
            $table->string("ip_address")->nullable();
            $table->string("user_agent")->nullable();
            $table->string("country")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("scans");
    }
};
