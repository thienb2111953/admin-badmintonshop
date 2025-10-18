// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { ThanhToan } from '@/types';

export function columns(): ColumnDef<ThanhToan>[] {
  return [
    {
      accessorKey: 'ma_don_hang',
      header: ({ column }) => <ColumnHeader column={column} title="Mã đơn hàng" />,
      cell: ({ row }) => row.original.ma_don_hang,
    },
   {
      accessorKey: 'so_tien',
      header: ({ column }) => <ColumnHeader column={column} title="Số tiền" />,
      cell: ({ row }) => {
        const value = Number(row.original.so_tien || 0)
        return value.toLocaleString('vi-VN') + ' ₫'
      }
    },
    {
      accessorKey: 'ten_ngan_hang',
      header: ({ column }) => <ColumnHeader column={column} title="Ngân hàng" />,
    },
    {
      accessorKey: 'ngay_thanh_toan',
      header: ({ column }) => <ColumnHeader column={column} title="Ngày thanh toán" />,
      cell: ({ row }) => new Date(row.original.ngay_thanh_toan).toLocaleDateString('vi-VN'),
    },
   
  ];
}
