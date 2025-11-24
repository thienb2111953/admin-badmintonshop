<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\KhuyenMai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    public function kiemTraTonKho($id_san_pham_chi_tiet, $so_luong)
    {
        $sanPhamChiTiet = DB::table('san_pham_chi_tiet')
            ->where('id_san_pham_chi_tiet', $id_san_pham_chi_tiet)
            ->first();

        if (!$sanPhamChiTiet) {
            return "Sản phẩm không tồn tại";
        }

        if ($sanPhamChiTiet->so_luong_ton < $so_luong) {
            return "Sản phẩm không đáp ứng đủ số lượng";

        }

//            if ($sanPhamChiTiet->so_luong_ton < $sanPhamChiTiet->so_luong) {
//                $tenSanPham = DB::table('san_pham_chi_tiet')
//                    ->where('id_san_pham_chi_tiet', $sanPhamChiTiet->id_san_pham_chi_tiet)
//                    ->value('ten_san_pham_chi_tiet');
//
//                return "Sản phẩm {$tenSanPham} không đủ hàng";
//            }

        return true;
    }

    public function taoDonHang(Request $request)
    {
        try {
            return DB::transaction(function () use ($request) {

                $sanPhamArr = $request->input('san_pham');
                $sanPhamArr = [
                    [
                        'id_san_pham_chi_tiet' => 726,
                        'so_luong' => 1,
                    ],
                    [
                        'id_san_pham_chi_tiet' => 727,
                        'so_luong' => 2,
                    ],
                    [
                        'id_san_pham_chi_tiet' => 728,
                        'so_luong' => 3,
                    ],
                ];
                $id_dia_chi_nguoi_dung = $request->input('id_dia_chi_nguoi_dung');

                if (!is_array($sanPhamArr) || count($sanPhamArr) === 0) {
                    return response()->json(['message' => 'Không có sản phẩm nào được chọn'], 400);
                }

                // ================================
                // 1️⃣ TẠO ĐƠN HÀNG
                // ================================
                $ma_don_hang = 'DH' . strtoupper(uniqid());
                $id_don_hang = DB::table('don_hang')->insertGetId([
                    'id_dia_chi_nguoi_dung' => $id_dia_chi_nguoi_dung,
                    'ma_don_hang' => $ma_don_hang,
                    'trang_thai_don_hang' => 'Đang xử lý',
                    'trang_thai_thanh_toan' => 'Chưa thanh toán',
                    'phuong_thuc_thanh_toan' => 'VNPay',
                    'ngay_dat_hang' => now(),
                ], 'id_don_hang');

                $tongTien = 0;

                // ================================
                // 2️⃣ XỬ LÝ TỪNG SẢN PHẨM
                // ================================
                foreach ($sanPhamArr as $item) {

                    $id_san_pham_chi_tiet = $item['id_san_pham_chi_tiet'];
                    $so_luong            = (int) $item['so_luong'];

                    // 2.1 – Kiểm tra tồn kho
                    $kiemTra = $this->kiemTraTonKho($id_san_pham_chi_tiet, $so_luong);
                    if ($kiemTra !== true) {
                        return response()->json(['message' => $kiemTra], 400);
                    }

                    // 2.2 – Lấy chi tiết SP
                    $spct = DB::table('san_pham_chi_tiet')
                        ->where('id_san_pham_chi_tiet', $id_san_pham_chi_tiet)
                        ->first();

                    if (!$spct) {
                        return response()->json(['message' => 'Sản phẩm không tồn tại'], 400);
                    }

                    $donGia = $spct->gia_ban;

                    // 2.3 – Check khuyến mãi sản phẩm
                    $kmSP = DB::table('san_pham_khuyen_mai')
                        ->leftJoin('khuyen_mai', 'khuyen_mai.id_khuyen_mai', '=', 'san_pham_khuyen_mai.id_khuyen_mai')
                        ->where('san_pham_khuyen_mai.id_san_pham', $spct->id_san_pham)
                        ->where('khuyen_mai.ngay_ket_thuc', '>=', now())
                        ->orderBy('san_pham_khuyen_mai.id_san_pham_khuyen_mai', 'desc')
                        ->first();

                    if ($kmSP) {
                        if ($kmSP->don_vi_tinh == 'percent') {
                            $donGia = $donGia - ($donGia * $kmSP->gia_tri / 100);
                            $donGia = round($donGia / 1000) * 1000;
                        } else {
                            $donGia = $donGia - $kmSP->gia_tri;
                        }
                    }

                    // 2.4 – Insert chi tiết đơn hàng
                    DB::table('don_hang_chi_tiet')->insert([
                        'id_don_hang'          => $id_don_hang,
                        'id_san_pham_chi_tiet' => $id_san_pham_chi_tiet,
                        'so_luong'             => $so_luong,
                        'don_gia'              => $donGia,
                    ]);

                    // 2.5 – Trừ tồn kho
                    DB::table('san_pham_chi_tiet')
                        ->where('id_san_pham_chi_tiet', $id_san_pham_chi_tiet)
                        ->decrement('so_luong_ton', $so_luong);

                    // 2.6 – Cộng tiền
                    $tongTien += $donGia * $so_luong;
                }

                // ================================
                // 3️⃣ KHÁM GIÁ / KM ĐƠN HÀNG
                // ================================
                $kmDH = DB::table('don_hang_khuyen_mai')
                    ->leftJoin('khuyen_mai', 'khuyen_mai.id_khuyen_mai', '=', 'don_hang_khuyen_mai.id_khuyen_mai')
                    ->where('khuyen_mai.ngay_ket_thuc', '>=', now())
                    ->orderBy('khuyen_mai.id_khuyen_mai', 'desc')
                    ->first();

                if ($kmDH && $tongTien > $kmDH->gia_tri_duoc_giam) {
                    if ($kmDH->don_vi_tinh === 'percent') {
                        $tongTien = $tongTien - ($tongTien * $kmDH->gia_tri / 100);
                    } else {
                        $tongTien = $tongTien - $kmDH->gia_tri;
                    }
                }

                // ================================
                // 4️⃣ UPDATE TỔNG TIỀN
                // ================================
                DB::table('don_hang')
                    ->where('id_don_hang', $id_don_hang)
                    ->update(['tong_tien' => $tongTien]);

                return response()->json([
                    'id_don_hang' => $id_don_hang,
                    'ma_don_hang' => $ma_don_hang,
                    'tong_tien'   => $tongTien,
                ], 201);
            });

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Lỗi đặt hàng, đã rollback!',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function vnpayPayment(Request $request)
    {
//        $data = request()->all();

        $id_don_hang = $request->input('id_don_hang');
        $ma_don_hang = $request->input('ma_don_hang');
        $tong_tien = $request->input('tong_tien');


        if (!$id_don_hang || !$tong_tien) {
            return response()->json(['message' => 'Thiếu thông tin đơn hàng.'], 400);
        }


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

        return response()->json([
            'vnp_url' => $vnp_Url,
        ]);

//        header('Location: ' . $vnp_Url);
//        die();

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

            return redirect('http://localhost:3000')->with('success', 'Thanh toán thành công');
        } else {
            // ❌ Thanh toán thất bại → rollback
            $this->xuLySauThanhToanThatBai($id_don_hang);

            return redirect('http://localhost:3000')->with('success', 'Thanh toán thất bại');
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
