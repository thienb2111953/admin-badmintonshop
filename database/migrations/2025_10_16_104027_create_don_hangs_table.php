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
            $table->foreignId('id_dia_chi_nguoi_dung')->nullable()->constrained('dia_chi_nguoi_dung', 'id_dia_chi_nguoi_dung')->onDelete('set null');
            $table->string('trang_thai_thanh_toan', 50)->default('Chưa thanh toán');
            $table->string('trang_thai_don_hang', 50)->default('Đang xử lý');
            $table->string('phuong_thuc_thanh_toan', 50)->nullable();
            $table->decimal('tong_tien', 15, 0);
            $table->dateTime('ngay_dat_hang')->useCurrent();
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
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
