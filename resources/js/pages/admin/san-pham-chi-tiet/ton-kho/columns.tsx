// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { SanPhamChiTiet } from '@/types';
import { Link } from '@inertiajs/react';

export function Columns(
  onEdit: (row: SanPhamChiTiet) => void,
  onDelete: (row: SanPhamChiTiet) => void,
): ColumnDef<SanPhamChiTiet>[] {
  return [
    {
      accessorKey: 'ten_mau',
      header: ({ column }) => <ColumnHeader column={column} title="Màu" />,
    },
    {
      accessorKey: 'ten_kich_thuoc',
      header: ({ column }) => <ColumnHeader column={column} title="Kích thước" />,
    },
    {
      accessorKey: 'so_luong_ton',
      header: ({ column }) => <ColumnHeader column={column} title="Số lượng tồn" />,
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
