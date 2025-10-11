// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { DanhMucThuongHieu } from '@/types';
import { Link } from '@inertiajs/react';
import { san_pham } from '@/routes';

export function columns(
  onEdit: (row: DanhMucThuongHieu) => void,
  onDelete: (row: DanhMucThuongHieu) => void,
): ColumnDef<DanhMucThuongHieu>[] {
  return [
    {
      accessorKey: 'ten_danh_muc_thuong_hieu',
      header: ({ column }) => <ColumnHeader column={column} title="Tên danh mục thương hiệu" />,
      cell: ({ row }) => {
        const rowData = row.original;
        return (
          <Link href={san_pham(rowData.id_danh_muc_thuong_hieu)} className="font-bold hover:underline">
            {rowData.ten_danh_muc_thuong_hieu}
          </Link>
        );
      },
    },
    {
      accessorKey: 'slug',
      header: ({ column }) => <ColumnHeader column={column} title="Slug" />,
    },
    // {
    //   accessorKey: 'mo_ta',
    //   header: ({ column }) => <ColumnHeader column={column} title="Mô tả" />,
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
