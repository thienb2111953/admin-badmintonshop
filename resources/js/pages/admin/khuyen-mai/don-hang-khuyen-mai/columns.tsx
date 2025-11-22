// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { DonHangKhuyenMai } from '@/types';

export function Columns(
  onEdit: (row: DonHangKhuyenMai) => void,
  onDelete: (row: DonHangKhuyenMai) => void,
): ColumnDef<DonHangKhuyenMai>[] {
  return [
    {
      accessorKey: 'ma_khuyen_mai',
      header: ({ column }) => <ColumnHeader column={column} title="Mã khuyến mãi" />,
    },
      {
          accessorKey: 'gia_tri_duoc_giam',
          header: ({ column }) => <ColumnHeader column={column} title="Mã khuyến mãi có hiệu lực" />,
          cell: ({ row }) => {
              const raw = row.getValue('gia_tri_duoc_giam');
              const value = Number(raw);

              return isNaN(value) ? '' : value.toLocaleString('vi-VN') + ' đ';
          },
      },
      // {
      //     accessorKey: 'gia_sau_khuyen_mai',
      //     header: ({ column }) => <ColumnHeader column={column} title="Giá sau khuyến mãi" />,
      //     cell: ({ row }) => {
      //         const raw = row.getValue('gia_sau_khuyen_mai');
      //         const value = Number(raw);
      //
      //         return isNaN(value) ? '' : value.toLocaleString('vi-VN') + ' đ';
      //     },
      // },

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
