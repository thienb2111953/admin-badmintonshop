// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { Banner } from '@/types';
import { Badge } from '@/components/ui/badge';

export function columns(onEdit: (row: Banner) => void, onDelete: (row: Banner) => void): ColumnDef<Banner>[] {
  return [
    {
      accessorKey: 'img_url',
      header: ({ column }) => <ColumnHeader column={column} title="Hình ảnh" />,
      cell: ({ row }) => {
        const logoPath = row.original.img_url ?? '';
        const fullUrl = logoPath ? `/storage/${logoPath}` : '';

        return logoPath ? (
          <img src={fullUrl} alt="Hình ảnh" className="h-20 w-20 rounded object-contain" />
        ) : (
          <span className="text-gray-400">Chưa có hình ảnh</span>
        );
      },
    },
    {
      accessorKey: 'thu_tu',
      header: ({ column }) => <ColumnHeader column={column} title="Thứ tự trình chiếu hình ảnh" />,
      cell: ({ row }) => {
        return(
          <Badge className={"text-lg"} variant="outline">{row.original.thu_tu}</Badge>
        )
      },
    },
    {
      accessorKey: 'href',
      header: ({ column }) => <ColumnHeader column={column} title="href" />,
      cell: ({ row }) => {
        return(
          <a href={row.original.href} target='_blank'>{row.original.href}</a>
        )
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
