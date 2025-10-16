<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('don_hang_chi_tiet', function (Blueprint $table) {
      $table->id('id_don_hang_chi_tiet');
      $table->foreignId('id_don_hang')->constrained('don_hang', 'id_don_hang')->onDelete('cascade');

      $table
        ->foreignId('id_san_pham_chi_tiet')
        ->constrained('san_pham_chi_tiet', 'id_san_pham_chi_tiet')
        ->onDelete('restrict');

      $table->integer('so_luong');
      $table->decimal('don_gia', 15, 2);
      $table->decimal('tong_tien', 15, 2);

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('don_hang_chi_tiet');
  }
};
