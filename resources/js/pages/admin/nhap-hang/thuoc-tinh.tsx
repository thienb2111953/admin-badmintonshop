import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, ThuocTinh } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { thuoc_tinh } from '@/routes';

export default function ThuocTinhPage({ thuoc_tinhs }: { thuoc_tinhs: ThuocTinh[] }) {
  console.log(thuoc_tinhs);
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<ThuocTinh | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý thuộc tính', href: thuoc_tinh() }];
  const form = useForm<ThuocTinh>({
    id_thuoc_tinh: 0,
    ten_thuoc_tinh: '',
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      ten_thuoc_tinh: '',
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: ThuocTinh) => {
    setSelectedRow(row);
    form.setData({
      id_thuoc_tinh: row.id_thuoc_tinh,
      ten_thuoc_tinh: row.ten_thuoc_tinh,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: ThuocTinh) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('thuoc_tinh.destroy'), {
      data: { id_thuoc_tinh: selectedRow?.id_thuoc_tinh },
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
      form.put(route('thuoc_tinh.update'), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('thuoc_tinh.store'), {
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
      <Head title="Quản lý Thuộc tính" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={thuoc_tinhs} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa thuộc tính' : 'Thêm thuộc tính'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
