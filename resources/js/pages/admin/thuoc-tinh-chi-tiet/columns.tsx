// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { ThuocTinhChiTiet } from '@/types';

export function columns(
  onEdit: (row: ThuocTinhChiTiet) => void,
  onDelete: (row: ThuocTinhChiTiet) => void,
): ColumnDef<ThuocTinhChiTiet>[] {
  return [
    {
      accessorKey: 'ten_thuoc_tinh_chi_tiet',
      header: ({ column }) => <ColumnHeader column={column} title="Tên thuộc tính chi tiết" />,
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
