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
        Schema::create('yeu_cau_bao_hanh', function (Blueprint $table) {
            $table->id('id_yeu_cau_bao_hanh');
            $table->unsignedBigInteger('id_nguoi_dung');
            $table->foreign('id_nguoi_dung')
                ->references('id_nguoi_dung')
                ->on('nguoi_dung')
                ->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->string('ma_don_hang');
            $table->text('description')->nullable();
            $table->json('attachment')->nullable();
            $table->enum('status', [
                'pending',
                'received',
                'processing',
                'approved',
                'rejected',
                'completed'
            ])->default('pending');
            $table->text('admin_note')->nullable();
            $table->text('admin_response')->nullable();
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yeu_cau_bao_hanh');
    }
};
