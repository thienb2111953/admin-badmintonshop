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
        
        Schema::create('anh_san_pham', function (Blueprint $table) {
            $table->id('id_anh_san_pham');
            $table->string('anh_url');
            $table->integer('chieu_dai')->nullable();
            $table->integer('chieu_rong')->nullable();
            $table->unsignedBigInteger('id_san_pham');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anh_san_pham');
    }
};
