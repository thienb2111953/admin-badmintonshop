import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, NhapHang } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { nhap_hang } from '@/routes';

export default function NhapHangPage({ nhap_hangs }: { nhap_hangs: NhapHang[] }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<NhapHang | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý Nhập hàng', href: nhap_hang() }];
  const form = useForm<NhapHang>({
    id_nhap_hang: 0,
    ma_nhap_hang: '',
    ngay_nhap: new Date(),
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      ma_nhap_hang: '',
      ngay_nhap: new Date(),
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: NhapHang) => {
    setSelectedRow(row);
    form.setData({
      id_nhap_hang: row.id_nhap_hang,
      ma_nhap_hang: row.ma_nhap_hang,
      ngay_nhap: row.ngay_nhap,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: NhapHang) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('nhap_hang.destroy'), {
      data: { id_nhap_hang: selectedRow?.id_nhap_hang },
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
      form.put(route('nhap_hang.update'), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('nhap_hang.store'), {
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
      <Head title="Quản lý Nhập hàng" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={nhap_hangs} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa Nhập hàng' : 'Thêm Nhập hàng'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
