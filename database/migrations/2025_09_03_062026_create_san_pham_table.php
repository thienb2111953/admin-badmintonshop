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
            $table->text('mo_ta')->nullable();
            $table->decimal('gia_niem_yet')->nullable();
            $table->decimal('gia_ban')->nullable();
            $table->integer('id_mau')->nullable();
            $table->integer('id_kich_thuoc')->nullable();
            $table->integer('id_loai_san_pham')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('san_pham');
    }
};
