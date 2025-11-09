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
            $table->integer('thu_tu');
            $table->foreignId('id_san_pham_chi_tiet')
                ->constrained('san_pham_chi_tiet', 'id_san_pham_chi_tiet')
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
        Schema::dropIfExists('anh_san_pham');
    }
};
