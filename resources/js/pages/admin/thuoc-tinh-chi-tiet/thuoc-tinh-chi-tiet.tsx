import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, ThuocTinhChiTiet } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { thuoc_tinh, thuoc_tinh_chi_tiet } from '@/routes';
import { usePage } from '@inertiajs/react';

export default function ThuocTinhChiTietPage({
  id_thuoc_tinh,
  thuoc_tinh_chi_tiets,
}: {
  id_thuoc_tinh: number;
  thuoc_tinh_chi_tiets: ThuocTinhChiTiet[];
}) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<ThuocTinhChiTiet | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Quản lý thuộc tính', href: thuoc_tinh() },
    { title: 'Chi tiết', href: thuoc_tinh_chi_tiet({ id_thuoc_tinh }) },
  ];

  const form = useForm<ThuocTinhChiTiet>({
    id_thuoc_tinh: id_thuoc_tinh,
    id_thuoc_tinh_chi_tiet: 0,
    ten_thuoc_tinh_chi_tiet: '',
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      ten_thuoc_tinh_chi_tiet: '',
    });
    console.log(form.data);
    setOpenDialog(true);
  };

  const handleEdit = (row: ThuocTinhChiTiet) => {
    setSelectedRow(row);
    form.setData({
      id_thuoc_tinh_chi_tiet: row.id_thuoc_tinh_chi_tiet,
      ten_thuoc_tinh_chi_tiet: row.ten_thuoc_tinh_chi_tiet,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: ThuocTinhChiTiet) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('thuoc_tinh_chi_tiet.destroy', { id_thuoc_tinh: id_thuoc_tinh }), {
      data: { id_thuoc_tinh_chi_tiet: selectedRow?.id_thuoc_tinh_chi_tiet },
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
      form.put(route('thuoc_tinh_chi_tiet.update', { id_thuoc_tinh: id_thuoc_tinh }), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('thuoc_tinh_chi_tiet.store', { id_thuoc_tinh: id_thuoc_tinh }), {
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
      <Head title="Quản lý Thuộc tính chi tiết" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={thuoc_tinh_chi_tiets} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa thuộc tính chi tiết' : 'Thêm thuộc tính chi tiết'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
