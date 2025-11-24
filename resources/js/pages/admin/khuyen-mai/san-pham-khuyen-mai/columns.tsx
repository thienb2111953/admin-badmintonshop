// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { SanPhamKhuyenMai } from '@/types';

export function Columns(
  onEdit: (row: SanPhamKhuyenMai) => void,
  onDelete: (row: SanPhamKhuyenMai) => void,
): ColumnDef<SanPhamKhuyenMai>[] {
  return [
    {
      accessorKey: 'ten_san_pham',
      header: ({ column }) => <ColumnHeader column={column} title="Sản phẩm" />,
    },
    {
      accessorKey: 'ma_khuyen_mai',
      header: ({ column }) => <ColumnHeader column={column} title="Mã khuyến mãi" />,
    },
      {
          accessorKey: 'gia_ban',
          header: ({ column }) => <ColumnHeader column={column} title="Giá bán" />,
          cell: ({ row }) => {
              const raw = row.getValue('gia_ban');
              const value = Number(raw);

              return isNaN(value) ? '' : value.toLocaleString('vi-VN') + ' đ';
          },
      },
      {
          accessorKey: 'gia_sau_khuyen_mai',
          header: ({ column }) => <ColumnHeader column={column} title="Giá sau khuyến mãi" />,
          cell: ({ row }) => {
              const raw = row.getValue('gia_sau_khuyen_mai');
              const value = Number(raw);

              const final = Math.round(value / 1000) * 1000;
              return final.toLocaleString('vi-VN') + ' đ';
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
