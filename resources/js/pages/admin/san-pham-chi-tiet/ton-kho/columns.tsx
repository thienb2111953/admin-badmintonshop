// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { SanPhamChiTiet } from '@/types';
import { Link } from '@inertiajs/react';
import { format } from 'date-fns';

export function Columns(
  onEdit: (row: SanPhamChiTiet) => void,
  onDelete: (row: SanPhamChiTiet) => void,
): ColumnDef<SanPhamChiTiet>[] {
  return [
    {
      accessorKey: 'mau',
      header: ({ column }) => <ColumnHeader column={column} title="Màu" />,
      cell: ({ row }) => row.original.mau?.ten_mau ?? '',
    },
    {
      accessorKey: 'kich_thuoc',
      header: ({ column }) => <ColumnHeader column={column} title="Kích thước" />,
      cell: ({ row }) => row.original.kich_thuoc?.ten_kich_thuoc ?? '',
    },
    {
      accessorKey: 'so_luong_nhap', // khác key
      header: ({ column }) => <ColumnHeader column={column} title="Số lượng nhập" />,
      cell: ({ row }) => row.original.kho?.[0]?.so_luong_nhap ?? 0,
    },
    {
      accessorKey: 'ngay_nhap', // khác key
      header: ({ column }) => <ColumnHeader column={column} title="Ngày Nhập" />,
      cell: ({ row }) => {
        const dateStr = row.original.kho?.[0]?.ngay_nhap;
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
            <Button
              variant="ghost"
              size="sm"
              className="h-8 w-8 p-0 hover:bg-red-50 hover:text-red-600"
              onClick={() => onDelete(rowData)}
              title="Xóa"
            >
              <Trash2 className="h-4 w-4" />
            </Button>
          </div>
        );
      },
    },
  ];
}
