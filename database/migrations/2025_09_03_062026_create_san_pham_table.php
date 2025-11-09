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
    Schema::create('san_pham', function (Blueprint $table) {
      $table->id('id_san_pham');
      $table->string('ma_san_pham');
      $table->string('ten_san_pham');
      $table->string('slug');
      $table->text('mo_ta')->nullable();
      $table->string('trang_thai')->default('Đang sản xuất');
      $table->json('thuoc_tinh');
      $table
        ->foreignId('id_danh_muc_thuong_hieu')
        ->constrained('danh_muc_thuong_hieu', 'id_danh_muc_thuong_hieu')
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
    Schema::dropIfExists('san_pham');
  }
};
