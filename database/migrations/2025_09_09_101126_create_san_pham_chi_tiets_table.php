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
        Schema::create('san_pham_chi_tiet', function (Blueprint $table) {
            $table->id('id_san_pham_chi_tiet');
            $table->string('ten_san_pham_chi_tiet');
            $table->string('slug');
            $table->foreignId('id_san_pham')
                ->constrained('san_pham', 'id_san_pham')
                ->onDelete('cascade');
            $table->foreignId('id_mau')
                ->constrained('mau', 'id_mau')
                ->onDelete('cascade');
            $table->foreignId('id_kich_thuoc')
                ->constrained('kich_thuoc', 'id_kich_thuoc')
                ->onDelete('cascade');
            $table->foreignId('id_anh_san_pham')
                ->constrained('anh_san_pham', 'id_anh_san_pham')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_pham_chi_tiet');
    }
};
