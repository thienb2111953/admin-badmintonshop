// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { ThuocTinh } from '@/types';
import { Link } from '@inertiajs/react';
import { thuoc_tinh_chi_tiet } from '@/routes';

export function columns(onEdit: (row: ThuocTinh) => void, onDelete: (row: ThuocTinh) => void): ColumnDef<ThuocTinh>[] {
  return [
    {
      accessorKey: 'ten_thuoc_tinh',
      header: ({ column }) => <ColumnHeader column={column} title="Tên thuộc tính" />,
      cell: ({ row }) => {
        const rowData = row.original;
        return (
          <Link href={thuoc_tinh_chi_tiet(rowData.id_thuoc_tinh)} className="font-bold hover:underline">
            {rowData.ten_thuoc_tinh}
          </Link>
        );
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
