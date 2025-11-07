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
    Schema::create('nhap_hang_chi_tiet', function (Blueprint $table) {
      $table->id('id_nhap_hang_chi_tiet');

      // liên kết đến bảng nhập hàng
      $table->foreignId('id_nhap_hang')->constrained('nhap_hang', 'id_nhap_hang')->onDelete('cascade');

      // liên kết đến bảng sản phẩm chi tiết
      $table
        ->foreignId('id_san_pham_chi_tiet')
        ->constrained('san_pham_chi_tiet', 'id_san_pham_chi_tiet')
        ->onDelete('restrict');

      $table->integer('so_luong');
      $table->decimal('don_gia', 15, 0);

        $table->timestamp('created_at', 6)->useCurrent();
        $table->timestamp('updated_at', 6)->useCurrent();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('nhap_hang_chi_tiet');
  }
};
