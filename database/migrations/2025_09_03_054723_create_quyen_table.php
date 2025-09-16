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
        Schema::create('quyen', function (Blueprint $table) {
            $table->id('id_quyen');
            $table->string('ten_quyen');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('path', function (Blueprint $table) {
            $table->id('id_path');
            $table->string('ten_path');
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('quyen_acl', function (Blueprint $table) {
            $table->id('id_quyen_acl');
            $table->foreignId('id_quyen')
                ->constrained('quyen', 'id_quyen')
                ->onDelete('cascade');
            $table->foreignId('id_path')
                ->constrained('path', 'id_path')
                ->onDelete('cascade');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_write')->default(false);
            $table->boolean('is_update')->default(false);
            $table->boolean('is_delete')->default(false);
            $table->timestamps();
        });

        Schema::create('quyen_nguoi_dung', function (Blueprint $table) {
            $table->id('id_quyen_nguoi_dung');
            $table->foreignId('id_quyen')
                ->constrained('quyen', 'id_quyen')
                ->onDelete('cascade');
            $table->foreignId('id_nguoi_dung')
                ->constrained('nguoi_dung', 'id_nguoi_dung')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quyen');
        Schema::dropIfExists('path');
        Schema::dropIfExists('quyen_acl');
        Schema::dropIfExists('quyen_nguoi_dung');
    }
};
