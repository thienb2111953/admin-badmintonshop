// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { NhapHangChiTiet } from '@/types';

export function columns(
  onEdit: (row: NhapHangChiTiet) => void,
  onDelete: (row: NhapHangChiTiet) => void,
): ColumnDef<NhapHangChiTiet>[] {
  return [
    {
      accessorKey: 'ten_san_pham_chi_tiet',
      header: ({ column }) => <ColumnHeader column={column} title="Sản phẩm" />,
    },
    {
      accessorKey: 'don_gia',
      header: ({ column }) => <ColumnHeader column={column} title="Đơn giá" />,
      cell: ({ row }) => row.original.don_gia.toLocaleString('vi-VN'),
    },
    {
      accessorKey: 'so_luong',
      header: ({ column }) => <ColumnHeader column={column} title="Số lượng" />,
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
