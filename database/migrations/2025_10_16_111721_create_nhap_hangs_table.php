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
    Schema::create('nhap_hang', function (Blueprint $table) {
      $table->id('id_nhap_hang');
      $table->string('ma_nhap_hang')->unique();
      $table->dateTime('ngay_nhap');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('nhap_hang');
  }
};
