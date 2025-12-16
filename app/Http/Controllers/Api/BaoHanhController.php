<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WarrantyReceivedMail;
use App\Models\YeuCauBaoHanh;
use App\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BaoHanhController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'required|email|max:255',
            'ma_don_hang' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachment' => 'nullable|array',
            'attachment.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'attachment.*.image' => 'File tải lên phải là hình ảnh.',
            'attachment.*.max' => 'Hình ảnh không được vượt quá 5MB.',
        ]);

        if ($validator->fails()) {
            return Response::Error('Dữ liệu không hợp lệ', $validator->errors());
        }

        try {
            $imagePaths = [];

            // 2. Xử lý Upload ảnh (Nếu có)
            if ($request->hasFile('attachment')) {
                foreach ($request->file('attachment') as $file) {
                    // Lưu file vào thư mục storage/app/public/bao-hanh
                    // Cần chạy lệnh: php artisan storage:link
                    $path = $file->store('bao-hanh', 'public');

                    // Thêm đường dẫn vào mảng (ví dụ: /storage/bao-hanh/abc.jpg)
                    $imagePaths[] = '/storage/' . $path;
                }
            }

            // 3. Tạo bản ghi mới trong DB
            $yeuCau = YeuCauBaoHanh::create([
                'id_nguoi_dung' => Auth::guard('api')->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'ma_don_hang' => $request->ma_don_hang,
                'description' => $request->description,
                'attachment' => !empty($imagePaths) ? $imagePaths : null,
                'status' => 'pending'
            ]);

            try {
                Mail::to($request->customer_email)->send(new WarrantyReceivedMail($yeuCau));
            } catch (\Exception $e) {
                Log::error('Lỗi gửi mail bảo hành: ' . $e->getMessage());
            }

            return Response::Success($yeuCau, 'Gửi yêu cầu thành công !');

        } catch (\Exception $e) {
            return Response::Error('Có lỗi xảy ra', $e->getMessage());
        }
    }
}
