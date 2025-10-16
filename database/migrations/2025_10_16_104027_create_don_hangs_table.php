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
    Schema::create('don_hang', function (Blueprint $table) {
      $table->id('id_don_hang');
      $table->string('ma_don_hang')->unique();
      $table->foreignId('id_nguoi_dung')->nullable()->constrained('nguoi_dung', 'id_nguoi_dung')->onDelete('set null');
      $table->decimal('tong_tien');
      $table->string('trang_thai', 50);
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('don_hang');
  }
};
