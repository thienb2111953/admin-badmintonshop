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
            $table->unsignedBigInteger('id_thuoc_tinh');
            $table->foreign('id_thuoc_tinh')
                ->references('id_thuoc_tinh')
                ->on('thuoc_tinh')
                ->onDelete('cascade');
            $table->unsignedBigInteger('id_danh_muc');
            $table->foreign('id_danh_muc')
                ->references('id_danh_muc')
                ->on('danh_muc')
                ->onDelete('cascade');
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
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
