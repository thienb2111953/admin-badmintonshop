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
        Schema::create('thuoc_tinh_chi_tiet', function (Blueprint $table) {
            $table->id('id_thuoc_tinh_chi_tiet');
            $table->string('ten_thuoc_tinh_chi_tiet');
            $table->unsignedBigInteger('id_thuoc_tinh');
            $table->foreign('id_thuoc_tinh')
                ->references('id_thuoc_tinh')
                ->on('thuoc_tinh')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thuoc_tinh_chi_tiet');
    }
};
