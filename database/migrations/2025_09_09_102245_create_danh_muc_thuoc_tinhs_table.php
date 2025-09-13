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
        Schema::create('danh_muc_thuoc_tinh', function (Blueprint $table) {
            $table->id('id_danh_muc_thuoc_tinh');
            $table->integer('id_danh_muc')->nullable();
            $table->integer('id_thuoc_tinh')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_thuoc_tinh');
    }
};
