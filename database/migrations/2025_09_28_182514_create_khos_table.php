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
        Schema::create('kho', function (Blueprint $table) {
            $table->id('id_kho');
            $table->foreignId('id_san_pham_chi_tiet')
                ->constrained('san_pham_chi_tiet', 'id_san_pham_chi_tiet')
                ->onDelete('cascade');
            $table->integer('so_luong_nhap');
            $table->dateTime('ngay_nhap')->default(now());
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kho');
    }
};
