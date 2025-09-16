// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { DanhMuc } from '@/types';

export function columns(onEdit: (row: DanhMuc) => void, onDelete: (row: DanhMuc) => void): ColumnDef<DanhMuc>[] {
  return [
    {
      accessorKey: 'ten_danh_muc',
      header: ({ column }) => <ColumnHeader column={column} title="Tên danh mục" />,
    },
    {
      accessorKey: 'slug',
      header: ({ column }) => <ColumnHeader column={column} title="Slug" />,
    },
    {
      accessorKey: 'thuoc_tinhs',
      header: ({ column }) => <ColumnHeader column={column} title="Thuộc tính" />,
      cell: ({ row }) => {
        const thuocTinhs = row.original.thuoc_tinhs || [];
        // return <span>{thuocTinhs.map((t: any) => t.ten_thuoc_tinh).join(', ')}</span>;
        return (
          <ul className="list-disc pl-4">
            {thuocTinhs.map((thuoc_tinh) => (
              <li key={thuoc_tinh.id_thuoc_tinh}>{thuoc_tinh.ten_thuoc_tinh}</li>
            ))}
          </ul>
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
