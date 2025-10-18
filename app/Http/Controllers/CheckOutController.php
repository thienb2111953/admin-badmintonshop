<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{

  public function vnpayReturn(Request $request)
  {
    $vnpData = $request->all();

    $amount = $vnpData['vnp_Amount'] ?? null;
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
      $this->xuLySauThanhToanThanhCong($id_don_hang, $amount, $bankCode, $cardType, $ngayThanhToan);

      return redirect('http://127.0.0.1:8000')->with('success', 'Thanh toán thành công');
    } else {
      return redirect('http://127.0.0.1:8000/quyen')->with('error', 'Thanh toán thất bại');
    }
  }

  public function vnpayPayment(Request $request)
  {
    $data = request()->all();

    $id_don_hang = $this->taoDonHang($request);

    $code_cart = rand(00, 9999);

    $vnp_TmnCode = 'JAAFIQBW'; //Mã định danh merchant kết nối (Terminal Id)
    $vnp_HashSecret = '9C5TPD7IEBP1LECOWONHTEGEMZ0PF8EI'; //Secret key
    $vnp_Returnurl = 'http://127.0.0.1:8000/api/vnpay-return';
    $vnp_apiUrl = 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html';
    $vnp_Url = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';

    $vnp_TxnRef = rand(1, 10000); //Mã giao dịch thanh toán tham chiếu của merchant
    $vnp_OrderInfo = 'Thanh toan don hang ' . $code_cart;
    $vnp_OrderType = 'billpayment';
    $vnp_Amount = $data['total_vnpay']; // Số
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
      // 'vnp_TxnRef' => $vnp_TxnRef,
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

    // return view('checkout');
  }


  private function taoDonHang(Request $request)
  {
    $ma_don_hang = 'DH' . strtoupper(uniqid());

    $id_don_hang = DB::table('don_hang')->insertGetId([
      'id_nguoi_dung' => 1,
      'ma_don_hang' => $ma_don_hang,
      'trang_thai_don_hang' => 'Đang xử lý',
      'trang_thai_thanh_toan' => 'Chưa thanh toán',
      'phuong_thuc_thanh_toan' => 'VNPay',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $gioHangChiTietIds = $request->input('id_gio_hang_chi_tiet');

    foreach ($gioHangChiTietIds as $id) {
      $gio_hang_chi_tiet = DB::table('gio_hang_chi_tiet')
        ->where('id_gio_hang_chi_tiet', $id)
        ->first();

      if (!$gio_hang_chi_tiet) continue;

      DB::table('don_hang_chi_tiet')->insert([
        'id_don_hang' => $id_don_hang,
        'id_gio_hang_chi_tiet' => $id,
        'id_san_pham_chi_tiet' => $gio_hang_chi_tiet->id_san_pham_chi_tiet,
        'so_luong' => $gio_hang_chi_tiet->so_luong,
        'created_at' => now(),
        'updated_at' => now(),
      ]);
    }

    return $id_don_hang;
  }

  private function xuLySauThanhToanThanhCong(int $id_don_hang, float $amount, ?string $bankCode, ?string $cardType, ?string $ngayThanhToan)
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
      'phuong_thuc' => $cardType,
      'ten_ngan_hang' => $bankCode,
      'ngay_thanh_toan' => $ngayThanhToan,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    // ✅ 3. Trừ tồn kho
    $chiTietDonHangs = DB::table('don_hang_chi_tiet')
      ->where('id_don_hang', $id_don_hang)
      ->select('id_san_pham_chi_tiet', 'so_luong')
      ->get();

    foreach ($chiTietDonHangs as $item) {
      DB::table('san_pham_chi_tiet')
        ->where('id_san_pham_chi_tiet', $item->id_san_pham_chi_tiet)
        ->decrement('so_luong_ton', $item->so_luong);
    }
  }
}
