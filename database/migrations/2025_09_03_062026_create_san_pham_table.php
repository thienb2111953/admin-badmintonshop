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
        Schema::create('san_pham', function (Blueprint $table) {
            $table->id('id_san_pham');
            $table->string('ma_san_pham');
            $table->string('ten_san_pham');
            $table->text('mo_ta');
            $table->decimal('gia_niem_yet');
            $table->decimal('gia_ban');
            $table->integer('id_mau');
            $table->integer('id_kich_thuoc');
            $table->integer('id_loai_san_pham');
            $table->timestamps();

//            $table->foreign('id_mau')->references('id_mau')->on('mau');
//            $table->foreign('id_kich_thuoc')->references('id_kich_thuoc')->on('kich_thuoc');
        });

        // Bảng Ảnh sản phẩm
        Schema::create('anh_san_pham', function (Blueprint $table) {
            $table->id('id_anh_san_pham');
            $table->string('anh_url');
            $table->integer('chieu_dai')->nullable();
            $table->integer('chieu_rong')->nullable();
            $table->unsignedBigInteger('id_san_pham');
            $table->timestamps();

//            $table->foreign('id_san_pham')->references('id_san_pham')->on('san_pham')->onDelete('cascade');
        });

        // Bảng Sản phẩm tồn kho
        Schema::create('san_pham_ton_kho', function (Blueprint $table) {
            $table->id('id_san_pham_ton_kho');
            $table->unsignedBigInteger('id_san_pham');
            $table->integer('so_luong_ton')->default(0);
            $table->timestamps();

//            $table->foreign('id_san_pham')->references('id_san_pham')->on('san_pham')->onDelete('cascade');
        });

        // Bảng Kích thước
        Schema::create('kich_thuoc', function (Blueprint $table) {
            $table->id('id_kich_thuoc');
            $table->string('ten_kich_thuoc');
            $table->timestamps();
        });

        // Bảng Màu
        Schema::create('mau', function (Blueprint $table) {
            $table->id('id_mau');
            $table->string('ten_mau');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_pham');
        Schema::dropIfExists('anh_san_pham');
        Schema::dropIfExists('san_pham_ton_kho');
        Schema::dropIfExists('kich_thuoc');
        Schema::dropIfExists('mau');
    }
};
