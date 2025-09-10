// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { Quyen } from '@/types';

export function columns(onEdit: (row: Quyen) => void, onDelete: (row: Quyen) => void): ColumnDef<Quyen>[] {
  return [
    {
      accessorKey: 'name',
      header: ({ column }) => <ColumnHeader column={column} title="Họ tên" />,
    },
    {
      accessorKey: 'email',
      header: ({ column }) => <ColumnHeader column={column} title="Email" />,
    },
    {
      accessorKey: 'ngay_sinh',
      header: ({ column }) => <ColumnHeader column={column} title="Ngày sinh" />,
    },
    {
      accessorKey: 'sdt',
      header: ({ column }) => <ColumnHeader column={column} title="SĐT" />,
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
