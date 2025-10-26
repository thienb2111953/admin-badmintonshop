import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, DanhMucThuongHieu, SanPham } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { san_pham_thuong_hieu, san_pham } from '@/routes';

export default function SanPhamPage({ san_phams, info_dmth }: { san_phams: SanPham[]; info_dmth: DanhMucThuongHieu }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<SanPham | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [
    { title: `${info_dmth.ten_danh_muc_thuong_hieu}`, href: san_pham_thuong_hieu() },
    {
      title: `Quản lý sản phẩm`,
      href: san_pham({ id_danh_muc_thuong_hieu: info_dmth.id_danh_muc_thuong_hieu }),
    },
  ];

  const form = useForm<SanPham>({
    id_san_pham: 0,
    ma_san_pham: '',
    ten_san_pham: '',
    slug: '',
    mo_ta: '',
    trang_thai: '',
  });

  const handleAdd = () => {
    router.visit(route('san_pham.storeView', { id_danh_muc_thuong_hieu: info_dmth.id_danh_muc_thuong_hieu }));
  };

  const handleEdit = (row: SanPham) => {
    router.visit(
      route('san_pham.updateView', {
        id_danh_muc_thuong_hieu: info_dmth.id_danh_muc_thuong_hieu,
        id_san_pham: row.id_san_pham,
      }),
    );
  };

  const handleDelete = (row: SanPham) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('san_pham.destroy', { id_danh_muc_thuong_hieu: info_dmth.id_danh_muc_thuong_hieu }), {
      data: { id_san_pham: selectedRow?.id_san_pham },
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Xóa thành công!');
        setOpenConfirm(false);
      },
      onError: () => toast.error('Xóa thất bại!'),
    });
  };

  const handleSubmit = () => {
    if (selectedRow) {
      form.put(route('san_pham.update', { id_danh_muc_thuong_hieu: info_dmth.id_danh_muc_thuong_hieu }), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('san_pham.store', { id_danh_muc_thuong_hieu: info_dmth.id_danh_muc_thuong_hieu }), {
        onSuccess: () => {
          toast.success('Tạo mới thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    }
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Sản phẩm" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={san_phams} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa Sản phẩm' : 'Thêm Sản phẩm'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
