// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { ThuongHieu } from '@/types';
import { Link } from '@inertiajs/react';

export function columns(
  onEdit: (row: ThuongHieu) => void,
  onDelete: (row: ThuongHieu) => void,
): ColumnDef<ThuongHieu>[] {
  return [
    {
      accessorKey: 'ma_thuong_hieu',
      header: ({ column }) => <ColumnHeader column={column} title="Mã - Tên thương hiệu" />,
      cell: ({ row }) => {
        const { ma_thuong_hieu, ten_thuong_hieu } = row.original;
        return (
          <span>
            {ma_thuong_hieu} - {ten_thuong_hieu}
          </span>
        );
      },
    },
    {
      accessorKey: 'logo_url',
      header: ({ column }) => <ColumnHeader column={column} title="Logo" />,
      cell: ({ row }) => {
        const logoPath = row.original.logo_url ?? '';
        const fullUrl = logoPath ? `/storage/${logoPath}` : '';

        return logoPath ? (
          <img src={fullUrl} alt="Logo" className="h-20 w-20 rounded object-contain" />
        ) : (
          <span className="text-gray-400">No logo</span>
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
