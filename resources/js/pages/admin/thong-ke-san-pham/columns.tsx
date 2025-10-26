// admin/thong-ke-san-pham/columns.tsx

import { ColumnDef } from '@tanstack/react-table';
import { ColumnHeader } from '@/components/custom/column-header';

export interface ThongKeSanPham {
    thoi_gian: string;
    id_san_pham_chi_tiet: number;
    ten_san_pham_chi_tiet: string;
    so_luong_nhap: string; // Thường là string từ DB khi dùng SUM
    so_luong_ban: string;
    gia_nhap: string | null;
    gia_ban: string | null;
    loi_nhuan_uoc_tinh: string | null;
}

// Hàm tiện ích format tiền tệ
const formatCurrency = (value: string | number | null) => {
    const numValue = Number(value);
    if (isNaN(numValue)) {
        return 'N/A';
    }
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(numValue);
};

// Hàm tiện ích format số lượng
const formatNumber = (value: string | number | null) => {
    const numValue = Number(value);
    if (isNaN(numValue)) {
        return 0;
    }
    return numValue;
};

export const columns: ColumnDef<ThongKeSanPham>[] = [
    {
        accessorKey: 'thoi_gian',
        header: ({ column }) => <ColumnHeader column={column} title="Thời gian" />,
    },
    {
        accessorKey: 'ten_san_pham_chi_tiet',
        header: ({ column }) => <ColumnHeader column={column} title="Tên sản phẩm" />,
        cell: ({ row }) => (
            <div className="min-w-[250px] font-medium">
                {row.original.ten_san_pham_chi_tiet}
            </div>
        ),
    },
    {
        accessorKey: 'so_luong_nhap',
        header: ({ column }) => <ColumnHeader column={column} title="SL Nhập" />,
        cell: ({ row }) => (
            <div className="">{formatNumber(row.original.so_luong_nhap)}</div>
        ),
    },
    {
        accessorKey: 'so_luong_ban',
        header: ({ column }) => <ColumnHeader column={column} title="SL Bán" />,
        cell: ({ row }) => (
            <div className=" text-blue-600 font-semibold">
                {formatNumber(row.original.so_luong_ban)}
            </div>
        ),
    },
    {
        accessorKey: 'gia_nhap',
        header: ({ column }) => <ColumnHeader column={column} title="Giá nhập (TB)" />,
        cell: ({ row }) => (
            <div className="min-w-[120px]">
                {formatCurrency(row.original.gia_nhap)}
            </div>
        ),
    },
    {
        accessorKey: 'gia_ban',
        header: ({ column }) => <ColumnHeader column={column} title="Giá bán (TB)" />,
        cell: ({ row }) => (
            <div className="min-w-[120px]">
                {formatCurrency(row.original.gia_ban)}
            </div>
        ),
    },
    {
        accessorKey: 'loi_nhuan_uoc_tinh',
        header: ({ column }) => <ColumnHeader column={column} title="Lợi nhuận" />,
        cell: ({ row }) => (
            <div className="min-w-[130px] font-bold text-green-600">
                {formatCurrency(row.original.loi_nhuan_uoc_tinh)}
            </div>
        ),
    },
];
