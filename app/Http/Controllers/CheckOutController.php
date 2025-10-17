<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
  public function vnpayReturn(Request $request)
  {
    // Lấy tất cả tham số trả về từ VNPAY
    $vnpData = $request->all();

    // Lấy từng giá trị cụ thể
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
    $txnRef = $vnpData['vnp_TxnRef'] ?? null;

    // ✅ Kiểm tra thanh toán thành công
    if ($responseCode == '00' && $transactionStatus == '00') {
      // Ghi vào DB
      DB::table('thanh_toan')->insert([
        'id_don_hang' => 1,
        'so_tien' => $amount,
        'ma_giao_dich' => $txnRef,
        'ten_ngan_hang' => $bankCode,
        'ngay_thanh_toan' => $payDate,
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      return redirect()->route('quyen.index')->with('success', 'Thanh toán thành công');
    } else {
      return redirect()->route('quyen.index')->with('error', 'Thanh toán thất bại');
    }
  }


  public function vnpayPayment()
  {
    $data = request()->all();
    $code_cart = rand(00, 9999);

    $vnp_TmnCode = 'JAAFIQBW'; //Mã định danh merchant kết nối (Terminal Id)
    $vnp_HashSecret = '9C5TPD7IEBP1LECOWONHTEGEMZ0PF8EI'; //Secret key
    $vnp_Returnurl = 'http://127.0.0.1:8000/quyen';
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
      'vnp_TxnRef' => $vnp_TxnRef,
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
}
