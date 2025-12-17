<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn #{{ $orderData['id_don_hang'] }}</title>
    <style>
        @page {
            size: 80mm auto; /* Width 80mm, height auto-adjusts */
            margin: 0;
        }

        @media print {
            body {
                width: 80mm;
                margin: 0;
                padding: 10mm 5mm;
            }
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            width: 80mm;
            margin: 0 auto;
            padding: 10mm 5mm;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .section {
            margin: 10px 0;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .product-item {
            margin: 5px 0;
        }

        .product-name {
            font-weight: bold;
        }

        .product-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
        }

        .total {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            font-style: italic;
            font-size: 11px;
        }

        /* Hide print dialog elements */
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="header">
    <h2>BADMINTON SHOP</h2>
    <div>Hotline: 0123 456 789</div>
    <div style="margin-top: 10px; font-weight: bold;">HÓA ĐƠN BÁN HÀNG</div>
</div>

<div class="section">
    <div class="section-title">Mã đơn hàng: {{ $orderData['ma_don_hang'] }}</div>
    <div>Khách: {{ $orderData['dia_chi_giao_hang']['ten_nguoi_dung'] }}</div>
    <div>SĐT: {{ $orderData['dia_chi_giao_hang']['so_dien_thoai'] }}</div>
    <div>Địa Chỉ: {{ $orderData['dia_chi_giao_hang']['dia_chi'] }}</div>
    <div>Ngày đặt hàng: {{ $orderData['ngay_dat_hang'] }}</div>
</div>

<div class="section">
    <div class="section-title">Tên món</div>
    @foreach($orderData['san_pham'] as $sp)
        <div class="product-item">
            <div class="product-name">{{ $sp['ten_san_pham'] }}</div>
            <div class="product-detail">
                <span>{{ $sp['mau'] }} - {{ $sp['kich_thuoc'] }}</span>
                <span></span>
            </div>
            <div class="product-detail">
                <span>{{ number_format($sp['don_gia']) }} đ x {{ $sp['so_luong'] }}</span>
                <span>{{ number_format($sp['thanh_tien']) }}</span>
            </div>
        </div>
    @endforeach
</div>

<div class="info-row">
    <span>TT:</span>
    <span>COD</span>
</div>

<div class="total">
    <div class="info-row" style="font-size: 16px;">
        <span>TỔNG CỘNG:</span>
        <span>{{ number_format($orderData['tong_tien']) }} đ</span>
    </div>
</div>

<div class="footer">
    Cảm ơn quý khách & Hẹn gặp lại!
</div>
</body>
</html>
