// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { SanPham } from '@/types';
import { Link } from '@inertiajs/react';

export function columns(onEdit: (row: SanPham) => void, onDelete: (row: SanPham) => void): ColumnDef<SanPham>[] {
  return [
    {
      accessorKey: 'ma_san_pham',
      header: ({ column }) => <ColumnHeader column={column} title="Mã sản phẩm" />,
      cell: ({ row }) => {
        const rowData = row.original;
        return (
          <Link
            href={route('san_pham_chi_tiet', {
              id_san_pham: rowData.id_san_pham,
            })}
            className="font-bold hover:underline"
          >
            {rowData.ma_san_pham}
          </Link>
        );
      },
    },
    {
      accessorKey: 'ten_san_pham',
      header: ({ column }) => <ColumnHeader column={column} title="Tên sản phẩm" />,
    },
    {
      accessorKey: 'gia_niem_yet',
      header: ({ column }) => <ColumnHeader column={column} title="Giá niêm yết" />,
      cell: ({ row }) => {
        const raw = row.getValue('gia_niem_yet');
        const value = Number(raw);

        return isNaN(value) ? '' : value.toLocaleString('vi-VN');
      },
    },
    {
      accessorKey: 'gia_ban',
      header: ({ column }) => <ColumnHeader column={column} title="Giá bán" />,
      cell: ({ row }) => {
        const raw = row.getValue('gia_ban');
        const value = Number(raw);

        return isNaN(value) ? '' : value.toLocaleString('vi-VN');
      },
    },
    {
      accessorKey: 'mo_ta',
      header: ({ column }) => <ColumnHeader column={column} title="Mô tả" />,
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
