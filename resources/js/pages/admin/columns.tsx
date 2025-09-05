// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { SquarePen, Trash2 } from 'lucide-react';
import { DataTableColumnHeader } from './data-table-column-header';
import { Quyen } from '@/types';

export function columns(onEdit: (row: Quyen) => void, onDelete: (row: Quyen) => void): ColumnDef<Quyen>[] {
  return [
    {
      id: 'select',
      header: ({ table }) => (
        <Checkbox
          checked={table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate')}
          onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
          aria-label="Select all"
        />
      ),
      cell: ({ row }) => (
        <Checkbox
          checked={row.getIsSelected()}
          onCheckedChange={(value) => row.toggleSelected(!!value)}
          aria-label="Select row"
        />
      ),
      enableSorting: false,
      enableHiding: false,
    },
    {
      accessorKey: 'ten_quyen',
      header: ({ column }) => <DataTableColumnHeader column={column} title="Quyền" />,
    },
    // {
    //     accessorKey: 'status',
    //     header: ({ column }) => (
    //         <DataTableColumnHeader column={column} title="Status" />
    //     ),
    // },
    // {
    //     accessorKey: 'email',
    //     header: ({ column }) => (
    //         <DataTableColumnHeader column={column} title="Email" />
    //     ),
    // },
    // {
    //     accessorKey: 'amount',
    //     header: ({ column }) => (
    //         <DataTableColumnHeader column={column} title="Amount" />
    //     ),
    //     cell: ({ row }) => {
    //         const amount = parseFloat(row.getValue('amount'));
    //         const formatted = new Intl.NumberFormat('vi-VN', {
    //             style: 'currency',
    //             currency: 'VND',
    //             minimumFractionDigits: 0,
    //         }).format(amount);
    //
    //         return <div className="font-medium">{formatted}</div>;
    //     },
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
