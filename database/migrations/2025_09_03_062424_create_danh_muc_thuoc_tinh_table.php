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

        Schema::create('danh_muc', function (Blueprint $table) {
            $table->id('danh_muc');
            $table->string('ten_danh_muc');
            $table->string('slug');
            $table->timestamps();
        });

        Schema::create('thuoc_tinh', function (Blueprint $table) {
            $table->id('thuoc_tinh');
            $table->string('ten_thuoc_tinh');
            $table->timestamps();
        });

        Schema::create('danh_muc_thuoc_tinh', function (Blueprint $table) {
            $table->id('id_danh_muc_thuoc_tinh');
            $table->integer('id_danh_muc');
            $table->integer('id_thuoc_tinh');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danh_muc_thuoc_tinh');
        Schema::dropIfExists('thuoc_tinh');
        Schema::dropIfExists('danh_muc_thuoc_tinh');
    }
};
