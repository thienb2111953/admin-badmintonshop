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
    Schema::create('thanh_toan', function (Blueprint $table) {
      $table->id('id_thanh_toan');

      // liên kết đến đơn hàng
      $table->foreignId('id_don_hang')->constrained('don_hang', 'id_don_hang')->onDelete('cascade');

      $table->decimal('so_tien', 15);
      $table->string('ten_ngan_hang', 100)->nullable();
      $table->dateTime('ngay_thanh_toan');

      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('thanh_toan');
  }
};
