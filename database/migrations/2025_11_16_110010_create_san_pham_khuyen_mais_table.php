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
        Schema::create('san_pham_khuyen_mai', function (Blueprint $table) {
            $table->id('id_san_pham_khuyen_mai');

            $table->foreignId('id_san_pham')
                ->constrained('san_pham', 'id_san_pham')
                ->onDelete('cascade');

            $table->foreignId('id_khuyen_mai')
                ->constrained('khuyen_mai', 'id_khuyen_mai')
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
        Schema::dropIfExists('san_pham_khuyen_mai');
    }
};
