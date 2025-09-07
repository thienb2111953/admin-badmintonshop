import AppLayout from '@/layouts/app-layout';
import { columns } from '@/pages/admin/quyen/columns';
import { DataTable } from '@/pages/admin/quyen/data-table';
import { type BreadcrumbItem, Quyen } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import * as React from 'react';
import { DialogCreateUpdate } from '@/components/custom/dialog-create-update';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý người dùng', href: '/quyen' }];

export default function QuyenPage({ quyen }: { quyen: Quyen[] }) {
  const [openDialog, setOpenDialog] = React.useState(false);
  const [selectedRow, setSelectedRow] = React.useState<Quyen | null>(null);
  const [openConfirm, setOpenConfirm] = React.useState(false);
  const [rowsToDelete, setRowsToDelete] = React.useState<Quyen[]>([]);

  const form = useForm<Quyen>({
    id_quyen: 0,
    ten_quyen: '',
  });

  // Khi mở dialog, reset hoặc set dữ liệu
  React.useEffect(() => {
    if (selectedRow) {
      form.setData({
        id_quyen: selectedRow.id_quyen,
        ten_quyen: selectedRow.ten_quyen,
      });
    } else {
      form.reset({ id_quyen: 0, ten_quyen: '' });
    }
  }, [selectedRow]);

  const handleAdd = () => {
    setSelectedRow(null); // xóa bản ghi cũ
    form.reset({ id_quyen: 0, ten_quyen: '' }); // reset form
    setOpenDialog(true);
  };

  const handleEdit = (row: Quyen) => {
    setSelectedRow(row);
    form.setData({ id_quyen: row.id_quyen, ten_quyen: row.ten_quyen }); // load dữ liệu vào form
    setOpenDialog(true);
  };

  const handleDelete = (row: Quyen) => {
    setRowsToDelete([row]);
    setOpenConfirm(true);
  };

  const handleDeleteSelected = (selectedRows: Quyen[]) => {
    if (!selectedRows.length) {
      toast.error('Chưa chọn quyền nào.');
      return;
    }
    setRowsToDelete(selectedRows);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    if (!rowsToDelete.length) return;
    const ids = rowsToDelete.map((r) => r.id_quyen);

    router.delete(route('quyen.destroyMultiple'), {
      data: { ids },
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Xóa thành công!');
        setOpenConfirm(false);
        setRowsToDelete([]);
      },
      onError: () => toast.error('Xóa thất bại!'),
    });
  };

  const handleSubmit = () => {
    if (selectedRow) {
      // Cập nhật
      form.put(route('quyen.update', { quyen: form.data.id_quyen }), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
          form.reset({ id_quyen: 0, ten_quyen: '' });
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      // Thêm mới
      form.post(route('quyen.store'), {
        onSuccess: () => {
          toast.success('Tạo mới thành công!');
          setOpenDialog(false);
          form.reset({ id_quyen: 0, ten_quyen: '' });
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    }
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Quyền" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable
          columns={columns(handleEdit, handleDelete)}
          data={quyen}
          onAdd={handleAdd}
          onDeleteSelected={handleDeleteSelected}
        />
      </div>

      <DialogCreateUpdate
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa quyền' : 'Thêm quyền'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
