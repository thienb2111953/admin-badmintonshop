<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ExportSanPhamJSON extends Command
{
    // 1. Đặt tên lệnh (signature) để gọi sau này
    protected $signature = 'app:export-products';

    // 2. Mô tả lệnh
    protected $description = 'Chạy script Python để xuất dữ liệu sản phẩm';

    public function handle()
    {
        $this->info('Đang bắt đầu xuất dữ liệu...');

        // 3. Lấy đường dẫn tuyệt đối tới file Python (Quan trọng để tránh lỗi đường dẫn)
        $scriptPath = storage_path('app/python/export_san_pham.py');

        // Kiểm tra file có tồn tại không
        if (!file_exists($scriptPath)) {
            $this->error("Không tìm thấy file tại: $scriptPath");
            return Command::FAILURE;
        }

        // 4. Cấu hình lệnh chạy (Windows dùng 'py', Linux/Mac thường dùng 'python3')
        // Sử dụng Process để an toàn và bắt lỗi tốt hơn exec()
        $result = Process::run("py \"{$scriptPath}\"");

        // 5. Kiểm tra kết quả
        if ($result->successful()) {
            $this->info('Thành công!');
            $this->line($result->output()); // In ra output từ Python nếu có
            return Command::SUCCESS;
        } else {
            $this->error('Lỗi khi chạy Python script:');
            $this->error($result->errorOutput());
            return Command::FAILURE;
        }
    }
}
