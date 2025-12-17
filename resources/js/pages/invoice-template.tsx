import React from 'react'

interface InvoicePrintProps {
  orderData: OrderDetail
}

interface Address {
    id_dia_chi_nguoi_dung: string
    id: string
    ten_nguoi_dung: string
    so_dien_thoai: string
    email?: string
    dia_chi: string
    mac_dinh: boolean
}

interface ItemDetail {
    id_san_pham_chi_tiet: number
    ten_san_pham: string
    gia_ban: number
    so_luong: number
    anh_url: string
    mau: string
    kich_thuoc: string
    don_gia: number
    thanh_tien: number
}

interface OrderDetail {
    id_don_hang: number
    ngay_dat_hang: string
    trang_thai_don_hang: string
    phuong_thuc_thanh_toan: string
    tong_tien: number
    dia_chi_giao_hang: Address
    san_pham: ItemDetail[]
}

const formatCurrency = (value: number | string) => {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        maximumFractionDigits: 0,
    }).format(Number(value));
};
export const InvoicePrint = React.forwardRef<HTMLDivElement, InvoicePrintProps>(({ orderData }, ref) => {
  return (
    <div
      ref={ref}
      className='bg-white text-black font-mono w-[80mm] p-4 text-xs leading-relaxed mx-auto print:mx-0'
      style={{ fontSize: '12px', fontFamily: 'monospace' }}
    >
      {/* Header */}
      <div className='text-center border-b border-dashed border-black pb-3 mb-2'>
        <h2 className='text-xl font-bold uppercase mb-1'>BADMINTON</h2>
        <p className='text-[10px]'>Hotline: 0123 456 789</p>
      </div>

      {/* Thông tin đơn */}
      <div className='text-center mb-3'>
        <h1 className='text-base font-bold uppercase my-1'>HÓA ĐƠN BÁN HÀNG</h1>
        <p>
          #{orderData.id_don_hang} - {orderData.ngay_dat_hang}
        </p>
      </div>

      {/* Khách hàng */}
      <div className='border-b border-dashed border-black pb-2 mb-2 text-[11px]'>
        <p>
          <span className='font-bold'>Khách:</span> {orderData.dia_chi_giao_hang.ten_nguoi_dung}
        </p>
        <p>
          <span className='font-bold'>SĐT:</span> {orderData.dia_chi_giao_hang.so_dien_thoai}
        </p>
        <p>
          <span className='font-bold'>ĐC:</span> {orderData.dia_chi_giao_hang.dia_chi}
        </p>
      </div>

      {/* Danh sách món */}
      <div className='mb-2'>
        <div className='flex font-bold border-b border-black mb-1 pb-1 uppercase text-[10px]'>
          <span className='flex-1'>Tên món</span>
          <span className='w-8 text-center'>SL</span>
          <span className='w-16 text-right'>Tiền</span>
        </div>

        {orderData.san_pham.map((item, index) => (
          <div key={index} className='py-1 border-b border-dotted border-gray-300 last:border-0'>
            <div className='font-bold mb-0.5'>{item.ten_san_pham}</div>
            <div className='flex justify-between items-center text-[10px] text-gray-600'>
              <span>
                {item.mau} / {item.kich_thuoc}
              </span>
            </div>
            <div className='flex justify-between items-center mt-0.5'>
              <span className='text-[11px]'>
                {formatCurrency(item.don_gia)} x {item.so_luong}
              </span>
              <span className='font-bold'>{item.thanh_tien.toLocaleString('vi-VN')}</span>
            </div>
          </div>
        ))}
      </div>

      {/* Tổng kết */}
      <div className='border-t border-dashed border-black pt-2 mb-4'>
        <div className='flex justify-between mb-1'>
          <span>TT:</span>
          <span className='font-bold'>{orderData.phuong_thuc_thanh_toan}</span>
        </div>
        <div className='flex justify-between text-sm font-bold mt-2 border-t border-black pt-2'>
          <span className='uppercase'>Tổng cộng:</span>
          <span className='text-lg'>{formatCurrency(orderData.tong_tien)}</span>
        </div>
      </div>

      {/* Footer */}
      <div className='text-center text-[10px] italic mt-4'>
        <p>Cảm ơn quý khách & Hẹn gặp lại!</p>
      </div>
    </div>
  )
})

InvoicePrint.displayName = 'InvoicePrint'
