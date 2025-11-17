import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, DonHangKhuyenMai, KhuyenMai, SanPham, SanPhamKhuyenMai } from '@/types';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { khuyen_mai } from '@/routes';
import KhuyenMaiPage from './khuyen-mai';
import SanPhamKhuyenMaiPage from '@/pages/admin/khuyen-mai/san-pham-khuyen-mai/san-pham-khuyen-mai';
import DonHangKhuyenMaiPage from '@/pages/admin/khuyen-mai/don-hang-khuyen-mai/don-hang-khuyen-mai';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Quản lý khuyến mãi',
        href: khuyen_mai()
    },
];

export default function SanPhamChiTietPage({
   khuyen_mais,
    san_pham_khuyen_mais,
    san_phams,
   don_hang_khuyen_mais
}: {
    khuyen_mais: KhuyenMai[];
    san_pham_khuyen_mais: SanPhamKhuyenMai[];
    san_phams: SanPham[];
    don_hang_khuyen_mais: DonHangKhuyenMai[];
}) {
    const [activeTab, setActiveTab] = useState('khuyen-mai');

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Quản lý Khuyến mãi" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
                    <TabsList className="grid w-[50%] grid-cols-3">
                        <TabsTrigger value="khuyen-mai">Khai báo khuyến mãi</TabsTrigger>
                        <TabsTrigger value="san-pham-khuyen-mai">Sản phẩm khuyến mãi</TabsTrigger>
                        <TabsTrigger value="don-hang-khuyen-mai">Đơn hàng khuyến mãi</TabsTrigger>
                    </TabsList>

                    <TabsContent value="khuyen-mai" className="space-y-4">
                        <KhuyenMaiPage khuyen_mais={khuyen_mais} />
                    </TabsContent>

                    <TabsContent value="san-pham-khuyen-mai" className="space-y-4">
                        <SanPhamKhuyenMaiPage
                            san_pham_khuyen_mais={san_pham_khuyen_mais}
                            san_phams={san_phams}
                            khuyen_mais={khuyen_mais}
                        />
                    </TabsContent>

                    <TabsContent value="don-hang-khuyen-mai" className="space-y-4">
                        <DonHangKhuyenMaiPage
                            don_hang_khuyen_mais={don_hang_khuyen_mais}
                            khuyen_mais={khuyen_mais}
                        />
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
