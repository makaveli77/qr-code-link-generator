<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("qr_codes", function (Blueprint $table) {
            $table->id();
            $table->foreignId("link_id")->constrained()->onDelete("cascade");
            $table->string("color")->default("#000000");
            $table->string("background_color")->default("#FFFFFF");
            $table->integer("size")->default(300);
            $table->string("logo_path")->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("qr_codes");
    }
};
