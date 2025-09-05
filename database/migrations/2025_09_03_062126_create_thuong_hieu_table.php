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
        Schema::create('thuong_hieu', function (Blueprint $table) {
            $table->id('id_thuong_hieu');
            $table->string('ma_thuong_hieu');
            $table->string('ten_thuong_hieu');
            $table->text('logo_url');
            $table->timestamps();
        });

        Schema::create('thuong_hieu_danh_muc', function (Blueprint $table) {
            $table->id('id_thuong_hieu_danh_muc');
            $table->string('id_thuong_hieu');
            $table->string('id_danh_muc');
            $table->string('ten_thuong_hieu_danh_muc');
            $table->string('slug');
            $table->text('mo_ta');
            $table->timestamps();
        });

        Schema::create('loai_san_pham', function (Blueprint $table) {
            $table->id('id_loai_san_pham');
            $table->string('ten_loai_san_pham');
            $table->integer('id_thuong_hieu_danh_muc');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuong_hieu');
        Schema::dropIfExists('thuong_hieu_danh_muc');
        Schema::dropIfExists('loai_san_pham');
    }
};
