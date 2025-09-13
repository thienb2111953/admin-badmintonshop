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
        Schema::create('thuong_hieu_danh_muc', function (Blueprint $table) {
            $table->id('id_thuong_hieu_danh_muc');
            $table->string('ten_thuong_hieu_danh_muc');
            $table->string('slug')->nullable();
            $table->text('mo_ta')->nullable();
            $table->integer('id_thuong_hieu')->nullable();
            $table->integer('id_danh_muc')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuong_hieu_danh_muc');
    }
};
