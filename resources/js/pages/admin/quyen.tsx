import AppLayout from '@/layouts/app-layout';
import { columns } from '@/pages/admin/columns';
import { DataTable } from '@/pages/admin/data-table';
import { type BreadcrumbItem, Quyen } from '@/types';
import { Head, router } from '@inertiajs/react';
import * as React from 'react';
import { DialogCRUD } from './dialog-crud';
import { DialogConfirmDelete } from './dialog-confirm-delete';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý người dùng', href: '/quyen' }];

export default function QuyenPage({ quyen }: { quyen: Quyen[] }) {
  const [openDialog, setOpenDialog] = React.useState(false);
  const [selectedRow, setSelectedRow] = React.useState<Quyen | null>(null);

  const [openConfirm, setOpenConfirm] = React.useState(false);
  const [rowsToDelete, setRowsToDelete] = React.useState<Quyen[]>([]);

  const handleAdd = () => {
    setSelectedRow(null);
    setOpenDialog(true);
  };

  const handleEdit = (row: Quyen) => {
    setSelectedRow(row);
    setOpenDialog(true);
  };

  const handleDelete = (row: Quyen) => {
    setRowsToDelete([row]);
    setOpenConfirm(true);
  };

  const handleDeleteSelected = (selectedRows: Quyen[]) => {
    if (selectedRows.length === 0) {
      toast.error('Chưa chọn quyền nào.');
      return;
    }
    setRowsToDelete(selectedRows);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    if (rowsToDelete.length === 0) return;

    const isBulk = rowsToDelete.length > 1;
    const data = isBulk ? { ids: rowsToDelete.map((r) => r.id_quyen) } : undefined;

    router.delete('xoa-nhieu-quyen', {
      data,
      preserveScroll: true,
      onSuccess: () => {
        toast.success('Xóa thành công!');
        setOpenConfirm(false);
        setRowsToDelete([]);
      },
      onError: () => toast.error('Xóa thất bại!'),
    });
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Quyền" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable
          columns={columns(handleEdit, handleDelete)}
          data={quyen}
          onAdd={handleAdd}
          onDeleteSelected={(selectedRows) => handleDeleteSelected(selectedRows)}
        />
      </div>

      <DialogCRUD open={openDialog} row={selectedRow} onClose={() => setOpenDialog(false)} />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
