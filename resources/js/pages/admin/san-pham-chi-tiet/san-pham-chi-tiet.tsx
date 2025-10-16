import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem, SanPham, AnhSanPham, SanPhamChiTiet, Mau, KichThuoc } from '@/types';
import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { Tabs, TabsList, TabsTrigger, TabsContent } from '@/components/ui/tabs';
import { san_pham_thuong_hieu, san_pham, san_pham_chi_tiet } from '@/routes';
import AnhSanPhamPage from './anh-san-pham/anh-san-pham';
import TonKhoPage from './ton-kho/ton-kho';

export default function SanPhamChiTietPage({
  san_pham_info,
  anh_san_phams,
  san_pham_chi_tiets,
  maus,
  kich_thuocs,
}: {
  san_pham_info: SanPham;
  anh_san_phams: AnhSanPham[];
  san_pham_chi_tiets: SanPhamChiTiet[];
  maus: Mau[];
  kich_thuocs: KichThuoc[];
}) {
  const [activeTab, setActiveTab] = useState('ton-kho');

  const breadcrumbs: BreadcrumbItem[] = [
    { title: san_pham_info.danh_muc_thuong_hieu.ten_danh_muc_thuong_hieu, href: san_pham_thuong_hieu() },
    {
      title: `${san_pham_info.ma_san_pham} - ${san_pham_info.ten_san_pham}`,
      href: san_pham(san_pham_info.id_danh_muc_thuong_hieu),
    },
    { title: 'Quản lý sản phẩm chi tiết', href: san_pham_chi_tiet(san_pham_info.id_san_pham) },
  ];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Sản phẩm" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
          <TabsList className="grid w-80 grid-cols-2">
            <TabsTrigger value="ton-kho">Tồn kho</TabsTrigger>
            <TabsTrigger value="anh-san-pham">Ảnh sản phẩm</TabsTrigger>
          </TabsList>

          <TabsContent value="anh-san-pham" className="space-y-4">
            <AnhSanPhamPage anh_san_phams={anh_san_phams} san_pham_info={san_pham_info} />
          </TabsContent>

          <TabsContent value="ton-kho" className="space-y-4">
            <TonKhoPage
              san_pham_chi_tiets={san_pham_chi_tiets}
              san_pham_info={san_pham_info}
              maus={maus}
              kich_thuocs={kich_thuocs}
            />
          </TabsContent>
        </Tabs>
      </div>
    </AppLayout>
  );
}
