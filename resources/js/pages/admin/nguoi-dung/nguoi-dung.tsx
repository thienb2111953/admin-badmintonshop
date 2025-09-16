import AppLayout from '@/layouts/app-layout';
import { columns } from '@/pages/admin/nguoi-dung/columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, User } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { dashboard, nguoi_dung, thuong_hieu } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Quản lý á', href: thuong_hieu() },
  { title: 'Quản lý người dùng', href: nguoi_dung() },
];

export default function NguoiDungPage({ users }: { users: User[] }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<User | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const form = useForm<User>({
    id_nguoi_dung: 0,
    name: '',
    email: '',
    password: '',
    ngay_sinh: '',
    sdt: '',
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({ id_nguoi_dung: 0, name: '', email: '', password: '', ngay_sinh: '', sdt: '' });
    setOpenDialog(true);
  };

  const handleEdit = (row: User) => {
    setSelectedRow(row);
    form.setData({
      id_nguoi_dung: row.id_nguoi_dung,
      name: row.name,
      email: row.email,
      ngay_sinh: row.ngay_sinh,
      sdt: row.sdt,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: User) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('nguoi_dung.destroy'), {
      data: { id_nguoi_dung: selectedRow?.id_nguoi_dung },
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
      // Cập nhật
      form.put(route('nguoi_dung.update'), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      // Thêm mới
      form.post(route('nguoi_dung.store'), {
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
      <Head title="Quản lý Quyền" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={users} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        onSubmit={handleSubmit}
        form={form}
        title={selectedRow ? 'Cập nhật người dùng' : 'Thêm người dùng'}
        btnTitle={selectedRow ? 'Cập nhật' : 'Thêm'}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
