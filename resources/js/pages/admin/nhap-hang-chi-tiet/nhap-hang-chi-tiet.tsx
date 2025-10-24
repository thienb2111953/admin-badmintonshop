import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, NhapHang, NhapHangChiTiet, SanPham, SanPhamChiTiet } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { nhap_hang, nhap_hang_chi_tiet } from '@/routes';

export default function NhapHangChiTietPage({
  nhap_hang_info,
  nhap_hang_chi_tiets,
  san_pham_chi_tiets,
}: {
  nhap_hang_info: NhapHang;
  nhap_hang_chi_tiets: NhapHangChiTiet[];
  san_pham_chi_tiets: SanPhamChiTiet[];
}) {
  console.log (san_pham_chi_tiets)
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<NhapHangChiTiet | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Quản lý Nhập hàng', href: nhap_hang() },
    { title: `${nhap_hang_info.ma_nhap_hang}`, href: nhap_hang_chi_tiet(nhap_hang_info.id_nhap_hang) },
  ];

  const form = useForm<NhapHangChiTiet>({
    id_nhap_hang: nhap_hang_info.id_nhap_hang,
    id_nhap_hang_chi_tiet: 0,
    id_san_pham_chi_tiet: 0,
    so_luong: null,
    don_gia: 0,
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      id_nhap_hang: nhap_hang_info.id_nhap_hang,
      id_nhap_hang_chi_tiet: 0,
      id_san_pham_chi_tiet: 0,
      so_luong: null,
      don_gia: 0,
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: NhapHangChiTiet) => {
    setSelectedRow(row);
    form.setData({
      id_nhap_hang_chi_tiet: row.id_nhap_hang_chi_tiet,
      id_san_pham_chi_tiet: row.id_san_pham_chi_tiet,
      so_luong: row.so_luong,
      don_gia: row.don_gia,
      id_nhap_hang: row.id_nhap_hang,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: NhapHangChiTiet) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('nhap_hang_chi_tiet.destroy', { id_nhap_hang: nhap_hang_info.id_nhap_hang }), {
      data: { id_nhap_hang_chi_tiet: selectedRow?.id_nhap_hang_chi_tiet },
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
      form.put(route('nhap_hang_chi_tiet.update', { id_nhap_hang: nhap_hang_info.id_nhap_hang }), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('nhap_hang_chi_tiet.store', { id_nhap_hang: nhap_hang_info.id_nhap_hang }), {
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
      <Head title="Quản lý Nhập hàng chi tiết" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={nhap_hang_chi_tiets} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa Nhập hàng chi tiết' : 'Thêm Nhập hàng chi tiết'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
        sanPhamChiTietOptions={san_pham_chi_tiets}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
