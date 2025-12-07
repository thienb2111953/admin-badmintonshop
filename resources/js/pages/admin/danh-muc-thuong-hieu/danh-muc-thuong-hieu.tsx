import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, DanhMuc, DanhMucThuongHieu, ThuongHieu } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { san_pham_thuong_hieu } from '@/routes';
import axios from 'axios';

export default function DanhMucThuongHieuPage({
  danh_muc_thuong_hieus,
  danh_mucs,
  thuong_hieus,
}: {
  danh_muc_thuong_hieus: DanhMucThuongHieu[];
  danh_mucs: DanhMuc[];
  thuong_hieus: ThuongHieu[];
}) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<DanhMucThuongHieu | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);
const [isExporting, setIsExporting] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [{ title: `Quản lý Danh mục Thương hiệu`, href: san_pham_thuong_hieu() }];

  const form = useForm<DanhMucThuongHieu>({
    id_danh_muc_thuong_hieu: 0,
    ten_danh_muc_thuong_hieu: '',
    slug: '',
    mo_ta: '',
    id_danh_muc: 0,
    id_thuong_hieu: 0,
  });

  const handleAdd = () => {
    router.visit(route('danh_muc_thuong_hieu.storeView'));
  };

  const handleEdit = (row: DanhMucThuongHieu) => {
    router.visit(route('danh_muc_thuong_hieu.updateView', { id_danh_muc_thuong_hieu: row.id_danh_muc_thuong_hieu }));
  };

  const handleDelete = (row: DanhMucThuongHieu) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('danh_muc_thuong_hieu.destroy'), {
      data: { id_danh_muc_thuong_hieu: selectedRow?.id_danh_muc_thuong_hieu },
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Xóa thành công!');
        setOpenConfirm(false);
      },
      onError: () => toast.error('Xóa thất bại!'),
    });
  };

    const handleExport = async () => {
        if (isExporting) return; // Chặn spam click
        setIsExporting(true);

        // Sử dụng toast.promise của Sonner để hiển thị trạng thái loading/thành công/thất bại
        toast.promise(
            // Gọi API
            axios.post(route('danh_muc_thuong_hieu.export')),
            {
                loading: 'Đang chạy script Python...',
                success: (response) => {
                    setIsExporting(false);
                    // Bạn có thể log output từ python nếu cần: console.log(response.data.output)
                    return `Thành công: ${response.data.message}`;
                },
                error: (error) => {
                    setIsExporting(false);
                    const msg = error.response?.data?.message || 'Có lỗi xảy ra!';
                    return `Thất bại: ${msg}`;
                },
            }
        );
    };

    return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Danh mục Thương hiệu" />

        <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={danh_muc_thuong_hieus} onAdd={handleAdd} onExport={handleExport}/>
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa Danh mục thương hiệu' : 'Thêm Danh mục thương hiệu'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        danhMucOptions={danh_mucs}
        thuongHieuOptions={thuong_hieus}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
