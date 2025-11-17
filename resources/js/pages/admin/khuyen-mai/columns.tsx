// columns.ts
import { ColumnDef } from '@tanstack/react-table';
import { Button } from '@/components/ui/button';
import { SquarePen, Trash2 } from 'lucide-react';
import { ColumnHeader } from '@/components/custom/column-header';
import { KhuyenMai } from '@/types';
import { format } from 'date-fns';

export function Columns(
    onEdit: (row: KhuyenMai) => void,
    onDelete: (row: KhuyenMai) => void,
): ColumnDef<KhuyenMai>[] {
    return [
        {
            accessorKey: 'ma_khuyen_mai',
            header: ({ column }) => <ColumnHeader column={column} title="Mã khuyến mãi" />,
            cell: ({ row }) => row.getValue('ma_khuyen_mai'),
        },

        {
            accessorKey: 'ten_khuyen_mai',
            header: ({ column }) => <ColumnHeader column={column} title="Tên khuyến mãi" />,
            cell: ({ row }) => row.getValue('ten_khuyen_mai'),
        },

        {
            accessorKey: 'gia_tri',
            header: ({ column }) => <ColumnHeader column={column} title="Giá trị" />,
            cell: ({ row }) => {
                const value = Number(row.getValue('gia_tri'));
                const donVi = row.original.don_vi_tinh;

                if (isNaN(value)) return '';

                return donVi === 'percent'
                    ? `${value}%`
                    : value.toLocaleString('vi-VN') + ' đ';
            },
        },

        {
            accessorKey: 'don_vi_tinh',
            header: ({ column }) => <ColumnHeader column={column} title="Đơn vị tính" />,
            cell: ({ row }) => (
                row.original.don_vi_tinh === 'percent'
                    ? '%'
                    : 'VNĐ'
            ),
        },

        {
            accessorKey: 'ngay_bat_dau',
            header: ({ column }) => <ColumnHeader column={column} title="Ngày bắt đầu" />,
            cell: ({ row }) => {
                const date = new Date(row.getValue('ngay_bat_dau'));
                return format(date, 'dd/MM/yyyy HH:mm:ss');
            },
        },

        {
            accessorKey: 'ngay_ket_thuc',
            header: ({ column }) => <ColumnHeader column={column} title="Ngày kết thúc" />,
            cell: ({ row }) => {
                const date = new Date(row.getValue('ngay_ket_thuc'));
                return format(date, 'dd/MM/yyyy HH:mm:ss');
            },
        },

        {
            id: 'actions',
            header: () => <div className="text-right pr-2">Hành động</div>,
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
