<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    public function kiemTraTonKho($gioHangChiTietIds)
    {
        foreach ($gioHangChiTietIds as $id) {
            $sanPhamChiTiet = DB::table('gio_hang_chi_tiet')
                ->join('san_pham_chi_tiet', 'san_pham_chi_tiet.id_san_pham_chi_tiet', '=', 'gio_hang_chi_tiet.id_san_pham_chi_tiet')
                ->where('gio_hang_chi_tiet.id_gio_hang_chi_tiet', $id)
                ->first();

            if (!$sanPhamChiTiet) {
                return "Sản phẩm không tồn tại";
            }

            if ($sanPhamChiTiet->so_luong_ton < $sanPhamChiTiet->so_luong) {
                $tenSanPham = DB::table('san_pham_chi_tiet')
                    ->where('id_san_pham_chi_tiet', $sanPhamChiTiet->id_san_pham_chi_tiet)
                    ->value('ten_san_pham_chi_tiet');

                return "Sản phẩm {$tenSanPham} không đủ hàng";
            }
        }

        return true;
    }

    public function taoDonHang($id_nguoi_dung, array $sanPhamChiTietIds)
    {
        if (!is_array($sanPhamChiTietIds)) {
            return 'Không có sản phẩm nào được chọn';
        }

        $gioHangChiTietIds = DB::table('gio_hang_chi_tiet')
            ->join('gio_hang', 'gio_hang_chi_tiet.id_gio_hang', '=', 'gio_hang.id_gio_hang')
            ->where('gio_hang.id_nguoi_dung', $id_nguoi_dung)
            ->whereIn('gio_hang_chi_tiet.id_san_pham_chi_tiet', $sanPhamChiTietIds)
            ->pluck('gio_hang_chi_tiet.id_gio_hang_chi_tiet')
            ->toArray();

        if (empty($gioHangChiTietIds)) {
            return 'Không tìm thấy sản phẩm trong giỏ hàng';
        }

        $tong_tien = 0;

        // ✅ Kiểm tra tồn kho trước khi tạo đơn
        $kiemTra = $this->kiemTraTonKho($gioHangChiTietIds);
        if ($kiemTra !== true) {
            return $kiemTra; // trả message lỗi
        }

        // ✅ Tạo đơn hàng
        $ma_don_hang = 'DH' . strtoupper(uniqid());
        $id_don_hang = DB::table('don_hang')->insertGetId([
            'id_nguoi_dung' => 1,
            'ma_don_hang' => $ma_don_hang,
            'trang_thai_don_hang' => 'Đang xử lý',
            'trang_thai_thanh_toan' => 'Chưa thanh toán',
            'phuong_thuc_thanh_toan' => 'VNPay',
            'ngay_dat_hang' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id_don_hang');

        // ✅ Thêm chi tiết đơn hàng
        foreach ($gioHangChiTietIds as $id) {
            $gio_hang = DB::table('gio_hang_chi_tiet')->where('id_gio_hang_chi_tiet', $id)->first();

            if (!$gio_hang) continue;

            $sanPhamChiTiet = DB::table('san_pham_chi_tiet')
                ->where('san_pham_chi_tiet.id_san_pham_chi_tiet', $gio_hang->id_san_pham_chi_tiet)
                ->first();

            DB::table('don_hang_chi_tiet')->insert([
                'id_don_hang' => $id_don_hang,
                'id_san_pham_chi_tiet' => $gio_hang->id_san_pham_chi_tiet,
                'so_luong' => $gio_hang->so_luong,
                'don_gia' => $sanPhamChiTiet->gia_ban,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Nếu đủ hàng -> trừ tồn kho
            $gio_hang_chi_tiet = DB::table('gio_hang_chi_tiet')
                ->where('id_gio_hang_chi_tiet', $id)
                ->first();

            if ($gio_hang_chi_tiet) {
                DB::table('san_pham_chi_tiet')
                    ->where('id_san_pham_chi_tiet', $gio_hang_chi_tiet->id_san_pham_chi_tiet)
                    ->decrement('so_luong_ton', $gio_hang_chi_tiet->so_luong);
            }
        }

        // ✅ Tính tổng tiền
        $tong_tien = DB::table('don_hang_chi_tiet')
            ->where('id_don_hang', $id_don_hang)
            ->select(DB::raw('SUM(so_luong * don_gia) as tong_tien'))
            ->value('tong_tien');

        return [
            'id_don_hang' => $id_don_hang,
            'ma_don_hang' => $ma_don_hang,
            'tong_tien' => $tong_tien,
        ];
    }

    public function vnpayPayment()
    {
        $data = request()->all();

        $kiemTra = $this->kiemTraTonKho($data['id_gio_hang_chi_tiet']);

        if ($kiemTra !== true) {
            return response()->json([
                'message' => $kiemTra,
            ], 400);
        }

        $don_hang = $this->taoDonHang($data['id_gio_hang_chi_tiet']);

        if (!is_array($don_hang)) {
            return response()->json([
                'message' => $don_hang,
            ], 400);
        }

        $id_don_hang = $don_hang['id_don_hang'];
        $ma_don_hang = $don_hang['ma_don_hang'];
        $tong_tien = $don_hang['tong_tien'];


        $vnp_TmnCode = 'DO7YTD4A'; //Mã định danh merchant kết nối (Terminal Id)
        $vnp_HashSecret = 'ZVC6BIHYJ6AUGMT7ID6R92DQ95ZI54HV'; //Secret key
        $vnp_Returnurl = 'http://127.0.0.1:8000/api/vnpay-return';
        $vnp_apiUrl = 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html';
        $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';

        $vnp_TxnRef = rand(1, 10000); //Mã giao dịch thanh toán tham chiếu của merchant
        $vnp_OrderInfo = 'Thanh toan don hang ' . $ma_don_hang;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $tong_tien; // Số
        $vnp_Locale = 'vn';
        // $vnp_BankCode = 'NCB'; //Mã phương thức thanh toán
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR']; //IP Khách hàng thanh toán

        // $startTime = date('YmdHis');
        //$expire = date('YmdHis', strtotime('+5 minutes', strtotime($startTime)));

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount * 100,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => 'Thanh toan GD: ' . $vnp_TxnRef,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $id_don_hang,
            //   'vnp_ExpireDate' => $expire,
        ];

        if (isset($vnp_BankCode) && $vnp_BankCode != '') {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . '?' . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        header('Location: ' . $vnp_Url);
        die();

    }

    public function vnpayReturn(Request $request)
    {
        $vnpData = $request->all();

        $amount = ($vnpData['vnp_Amount'] ?? 0) / 100;
        $bankCode = $vnpData['vnp_BankCode'] ?? null;
        $bankTranNo = $vnpData['vnp_BankTranNo'] ?? null;
        $cardType = $vnpData['vnp_CardType'] ?? null;
        $orderInfo = $vnpData['vnp_OrderInfo'] ?? null;
        $payDate = $vnpData['vnp_PayDate'] ?? null;
        $responseCode = $vnpData['vnp_ResponseCode'] ?? null;
        $secureHash = $vnpData['vnp_SecureHash'] ?? null;
        $tmnCode = $vnpData['vnp_TmnCode'] ?? null;
        $transactionNo = $vnpData['vnp_TransactionNo'] ?? null;
        $transactionStatus = $vnpData['vnp_TransactionStatus'] ?? null;
        $id_don_hang = $vnpData['vnp_TxnRef'] ?? null;

        $ngayThanhToan = null;
        if ($payDate) {
            $ngayThanhToan = \Carbon\Carbon::createFromFormat('YmdHis', $payDate)->format('Y-m-d H:i:s');
        }

        if ($responseCode == '00' && $transactionStatus == '00') {
            // ✅ Thanh toán thành công
            $this->xuLySauThanhToanThanhCong($id_don_hang, $amount, $bankCode, $ngayThanhToan);

            return redirect('http://127.0.0.1:8000/dashboard')->with('success', 'Thanh toán thành công');
        } else {
            // ❌ Thanh toán thất bại → rollback
            $this->xuLySauThanhToanThatBai($id_don_hang);

            return redirect('http://127.0.0.1:8000/quyen')->with('error', 'Thanh toán thất bại');
        }
    }

    public function xuLySauThanhToanThanhCong(int $id_don_hang, float $amount, ?string $bankCode, ?string $ngayThanhToan)
    {
        // ✅ 1. Cập nhật trạng thái đơn hàng
        DB::table('don_hang')->where('id_don_hang', $id_don_hang)->update([
            'trang_thai_thanh_toan' => 'Đã thanh toán',
            'trang_thai_don_hang' => 'Đang xử lý',
            'updated_at' => now(),
        ]);

        // ✅ 2. Thêm bản ghi thanh toán
        DB::table('thanh_toan')->insert([
            'id_don_hang' => $id_don_hang,
            'so_tien' => $amount,
            'ten_ngan_hang' => $bankCode,
            'ngay_thanh_toan' => $ngayThanhToan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function xuLySauThanhToanThatBai(int $id_don_hang)
    {
        // 1. Lấy chi tiết đơn hàng để hoàn tồn kho
        $chiTiet = DB::table('don_hang_chi_tiet')
            ->where('id_don_hang', $id_don_hang)
            ->get();

        foreach ($chiTiet as $item) {
            DB::table('san_pham_chi_tiet')
                ->where('id_san_pham_chi_tiet', $item->id_san_pham_chi_tiet)
                ->increment('so_luong_ton', $item->so_luong);
        }

        // 2. Xóa chi tiết đơn hàng
        DB::table('don_hang_chi_tiet')->where('id_don_hang', $id_don_hang)->delete();

        // 3. Xóa đơn hàng chính
        DB::table('don_hang')->where('id_don_hang', $id_don_hang)->delete();
    }
}
