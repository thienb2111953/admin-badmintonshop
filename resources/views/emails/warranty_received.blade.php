<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { background-color: #f97316; color: white; padding: 15px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { padding: 20px; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .info-table th, .info-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .info-table th { background-color: #f4f4f4; width: 30%; }
        .footer { text-align: center; font-size: 12px; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .btn { display: inline-block; background-color: #f97316; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Đã Tiếp Nhận Yêu Cầu Bảo Hành</h2>
    </div>

    <div class="content">
        <p>Xin chào <strong>{{ $yeuCau->customer_name }}</strong>,</p>
        <p>Chúng tôi đã nhận được yêu cầu bảo hành của bạn. Dưới đây là thông tin chi tiết:</p>

        <table class="info-table">
            <tr>
                <th>Mã hồ sơ</th>
                <td><strong>#{{ $yeuCau->ma_don_hang }}</strong></td>
            </tr>
            <tr>
                <th>Mã đơn hàng</th>
                <td>{{ $yeuCau->ma_don_hang ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>SĐT liên hệ</th>
                <td>{{ $yeuCau->customer_phone }}</td>
            </tr>
            <tr>
                <th>Ngày gửi</th>
                <td>{{ $yeuCau->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Mô tả lỗi</th>
                <td>{{ $yeuCau->description }}</td>
            </tr>
            <tr>
                <th>Số lượng ảnh</th>
                <td>{{ is_array($yeuCau->attachment) ? count($yeuCau->attachment) : 0 }} file</td>
            </tr>
        </table>

        <p>Đội ngũ kỹ thuật sẽ kiểm tra và phản hồi lại cho bạn trong vòng <strong>24-48 giờ</strong> làm việc.</p>

        <p style="text-align: center;">
            <a href="{{ env('GOOGLE_REDIRECT_URI_CLIENT') }}" class="btn">Truy cập Website</a>
        </p>
    </div>

    <div class="footer">
        <p>Đây là email tự động, vui lòng không trả lời email này.</p>
        <p>Hotline hỗ trợ: 0123.456.789 | Email: support@badminton.shop.com</p>
    </div>
</div>
</body>
</html>
