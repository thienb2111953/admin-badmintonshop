// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { AnhSanPham } from '@/types';
import { Link } from '@inertiajs/react';

export function Columns(
  onEdit: (row: AnhSanPham) => void,
  onDelete: (row: AnhSanPham) => void,
): ColumnDef<AnhSanPham>[] {
  return [
    {
      accessorKey: 'ten_mau',
      header: ({ column }) => <ColumnHeader column={column} title="Màu" />,
    },
    {
      accessorKey: 'path_anh_san_pham_old',
      header: ({ column }) => <ColumnHeader column={column} title="Ảnh sản phẩm" />,
      cell: ({ row }) => {
        const files: { id_anh_san_pham: number; anh_url: string }[] = row.original.path_anh_san_pham_old || [];

        return (
          <div className="flex flex-wrap gap-2">
            {files.map((file, index) => (
              <img
                key={file.id_anh_san_pham || index}
                src={`/storage/${file.anh_url}`}
                alt={`Ảnh sản phẩm ${index + 1}`}
                className="h-16 w-16 rounded border object-cover"
              />
            ))}
          </div>
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
