// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from './column-header';
import { ThuongHieu } from '@/types';

interface CreateColumnsProps {
  setSelectedId: (id: string | number | null) => void;
  setOpenConfirm: (open: boolean) => void;
  onEdit: (data: any) => void;
}

export const createColumns = ({ setSelectedId, setOpenConfirm, onEdit }: CreateColumnsProps) => [
  {
    accessorKey: 'ma_thuong_hieu',
    header: ({ column }) => <ColumnHeader column={column} title="Mã thương hiệu" />,
  },
  {
    accessorKey: 'ten_thuong_hieu',
    header: ({ column }) => <ColumnHeader column={column} title="Tên thương hiệu" />,
  },
  {
    accessorKey: 'logo_url',
    // header: "Logo",
    // cell: ({ row }: any) => {
    //   const url = row.original.logo_url;
    //   return url ? (
    //     <img
    //       src={url}
    //       alt="Logo"
    //       className="h-10 w-10 object-contain rounded"
    //     />
    //   ) : (
    //     <span className="text-gray-400 italic">Không có</span>
    //   );
    // },
    header: ({ column }) => <ColumnHeader column={column} title="Logo" />,
  },
  {
    id: 'actions',
    cell: ({ row }: any) => {
      const rowData = row.original;
      const handleDelete = (data) => {
        setSelectedId(data.id_thuong_hieu);
        setOpenConfirm(true);
      };
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
            onClick={() => handleDelete(rowData)}
            title="Xóa"
          >
            <Trash2 className="h-4 w-4" />
          </Button>
        </div>
      );
    },
  },
];
