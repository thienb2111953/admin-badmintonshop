import AppLayout from '@/layouts/app-layout';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, DanhMuc } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { columns } from './columns';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { dashboard, nguoi_dung, thuong_hieu } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Quản lý á', href: thuong_hieu() },
  { title: 'Quản lý người dùng', href: nguoi_dung() },
];

export default function DanhMucPage({ danh_mucs }: { danh_mucs: DanhMuc[] }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<DanhMuc | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const form = useForm<DanhMuc>({
    id_danh_muc: 0,
    ten_danh_muc: '',
    slug: '',
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.reset();
    setOpenDialog(true);
  };

  const handleEdit = (row: DanhMuc) => {
    setSelectedRow(row);
    form.setData({
      id_danh_muc: row.id_danh_muc,
      ten_danh_muc: row.ten_danh_muc,
      slug: row.slug,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: DanhMuc) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('danh_muc.destroy'), {
      data: { id_danh_muc: selectedRow?.id_danh_muc },
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
      form.put(route('danh_muc.update'), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('danh_muc.store'), {
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
        <DataTable columns={columns(handleEdit, handleDelete)} data={danh_mucs} onAdd={handleAdd} />
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
