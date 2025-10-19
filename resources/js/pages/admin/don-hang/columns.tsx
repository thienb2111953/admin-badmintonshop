// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { DonHang } from '@/types';
import { Link } from '@inertiajs/react';
import { don_hang, don_hang_chi_tiet } from '@/routes';
import { format } from 'date-fns';

export function columns(onEdit: (row: DonHang) => void, onDelete: (row: DonHang) => void): ColumnDef<DonHang>[] {
  return [
    {
      accessorKey: 'ma_don_hang',
      header: ({ column }) => <ColumnHeader column={column} title="Mã đơn hàng" />,
      cell: ({ row }) => {
        const rowData = row.original;
        return (
          <Link href={don_hang_chi_tiet(rowData.id_don_hang)} className="font-bold hover:underline">
            {rowData.ma_don_hang}
          </Link>
        );
      },
    },
    {
      accessorKey: 'nguoi_dung_thong_tin',
      header: ({ column }) => <ColumnHeader column={column} title="Người dùng" />,
      cell: ({ row }) => row.original.nguoi_dung_thong_tin || '',
    },
    {
      accessorKey: 'phuong_thuc_thanh_toan',
      header: ({ column }) => <ColumnHeader column={column} title="Phương thức thanh toán" />,
    },
    {
      accessorKey: 'trang_thai_thanh_toan',
      header: ({ column }) => <ColumnHeader column={column} title="Trạng thái thanh toán" />,
    },
    {
      accessorKey: 'trang_thai_don_hang',
      header: ({ column }) => <ColumnHeader column={column} title="Trạng thái đơn hàng" />,
      cell: ({ row }) => {
        const status = row.original.trang_thai_don_hang;
        switch (status) {
          case 'Đang xử lý':
            return <span className="font-medium text-yellow-600">Đang xử lý</span>;
          case 'Vận chuyển':
            return <span className="font-medium text-blue-600">Vận chuyển</span>;
          case 'Đã nhận':
            return <span className="font-medium text-green-600">Đã nhận</span>;
          case 'Hủy':
            return <span className="font-medium text-red-600">Hủy</span>;
          default:
            return <span>{status}</span>;
        }
      },
    },
    {
      accessorKey: 'tong_tien',
      header: ({ column }) => <ColumnHeader column={column} title="Tổng tiền" />,
      cell: ({ row }) => {
        const value = Number(row.original.tong_tien || 0)
        return value.toLocaleString('vi-VN') + ' ₫'
      }
    },
    {
      accessorKey: 'ngay_dat_hang',
      header: ({ column }) => <ColumnHeader column={column} title="Ngày đặt hàng" />,
      cell: ({ row }) => {
        const dateStr = row.original.ngay_dat_hang;
        if (!dateStr) return '';
        return format(new Date(dateStr), 'dd/MM/yyyy');
      },
    },
    {
      id: 'actions',
      cell: ({ row }) => {
        const rowData = row.original;
        return (
          <div className="float-right flex items-center gap-2">
            <Button
              variant="ghost"
              size="sm"
              className="h-8 w-8 p-0 hover:bg-blue-50 hover:text-blue-600"
              onClick={() => onEdit(rowData)}
              title="Sửa"
            >
              <SquarePen className="h-4 w-4" />
            </Button>
            {/* <Button
              variant="ghost"
              size="sm"
              className="h-8 w-8 p-0 hover:bg-red-50 hover:text-red-600"
              onClick={() => onDelete(rowData)}
              title="Xóa"
            >
              <Trash2 className="h-4 w-4" />
            </Button> */}
          </div>
        );
      },
    },
  ];
}
