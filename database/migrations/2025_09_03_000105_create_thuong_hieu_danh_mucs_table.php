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
        Schema::create('danh_muc_thuong_hieu', function (Blueprint $table) {
            $table->id('id_danh_muc_thuong_hieu');
            $table->string('ten_danh_muc_thuong_hieu');
            $table->string('slug')->nullable();
            $table->text('mo_ta')->nullable();
            $table->unsignedBigInteger('id_thuong_hieu');
            $table->foreign('id_thuong_hieu')
                ->references('id_thuong_hieu')
                ->on('thuong_hieu')
                ->onDelete('cascade');
            $table->unsignedBigInteger('id_danh_muc');
            $table->foreign('id_danh_muc')
                ->references('id_danh_muc')
                ->on('danh_muc')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_thuong_hieu');
    }
};
