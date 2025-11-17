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
        Schema::create('khuyen_mai', function (Blueprint $table) {
            $table->id('id_khuyen_mai');

            $table->string('ma_khuyen_mai');
            $table->string('ten_khuyen_mai');
            $table->integer('gia_tri');
            $table->string('don_vi_tinh');

            $table->dateTime('ngay_bat_dau');
            $table->dateTime('ngay_ket_thuc');

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khuyen_mai');
    }
};
