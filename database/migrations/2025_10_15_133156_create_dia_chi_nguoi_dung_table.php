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
        Schema::create('dia_chi_nguoi_dung', function (Blueprint $table) {
            $table->id('id_dia_chi_nguoi_dung');
            $table->foreignId('id_nguoi_dung')->nullable()->constrained('nguoi_dung', 'id_nguoi_dung')->onDelete('set null');
            $table->string('ten_nguoi_dung');
            $table->string('dia_chi');
            $table->string('so_dien_thoai');
            $table->string('email')->nullable();
            $table->boolean('mac_dinh')->default(0);
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dia_chi_nguoi_dung');
    }
};
