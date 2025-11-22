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
        Schema::create('gio_hang_chi_tiet', function (Blueprint $table) {
            $table->increments('id_gio_hang_chi_tiet');
            $table->foreignId('id_gio_hang')->constrained('gio_hang', 'id_gio_hang')->onDelete('cascade');
            $table
                ->foreignId('id_san_pham_chi_tiet')
                ->constrained('san_pham_chi_tiet', 'id_san_pham_chi_tiet')
                ->onDelete('restrict');

            $table->integer('so_luong');

            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gio_hang_chi_tiet');
    }
};
