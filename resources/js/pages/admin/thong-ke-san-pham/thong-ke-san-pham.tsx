import AppLayout from '@/layouts/app-layout';
import { columns, type ThongKeSanPham } from './columns'; // Import columns và type
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react'; // Import router
import { useState } from 'react';
import { thong_ke } from '@/routes'; // Giả sử bạn đã định nghĩa route
import { Button } from '@/components/ui/button'; // Import Button

// Định nghĩa kiểu cho props filters
interface Filters {
    type: 'month' | 'quarter' | 'year';
}

interface Props {
    thong_ke_san_phams: ThongKeSanPham[];
    filters: Filters;
}

export default function ThongKeSanPhamPage({ thong_ke_san_phams, filters }: Props) {
    const [timeRange, setTimeRange] = useState(filters.type || 'month');

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Thống kê sản phẩm', href: thong_ke() }, // Cập nhật breadcrumb
    ];

    // Hàm xử lý khi nhấn nút lọc
    const handleFilterChange = (newType: 'month' | 'quarter' | 'year') => {
        setTimeRange(newType);

        // Dùng Inertia router để GET, không dùng axios
        router.get(
            thong_ke(), // Tên route của hàm index
            { type: newType }, // Dữ liệu filter (tham số query)
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Thống kê sản phẩm" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* === CÁC NÚT LỌC === */}
                <div className="flex gap-2 py-2">
                    <Button
                        onClick={() => handleFilterChange('month')}
                        variant={timeRange === 'month' ? 'default' : 'outline'}
                    >
                        Theo Tháng
                    </Button>
                    <Button
                        onClick={() => handleFilterChange('quarter')}
                        variant={timeRange === 'quarter' ? 'default' : 'outline'}
                    >
                        Theo Quý
                    </Button>
                    <Button
                        onClick={() => handleFilterChange('year')}
                        variant={timeRange === 'year' ? 'default' : 'outline'}
                    >
                        Theo Năm
                    </Button>
                </div>

                <DataTable columns={columns} data={thong_ke_san_phams} showAddButton={false}/>
            </div>
        </AppLayout>
    );
}
