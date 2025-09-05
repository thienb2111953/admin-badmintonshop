import AppLayout from '@/layouts/app-layout';
import { columns, Payment } from '@/pages/admin/columns';
import { DataTable } from '@/pages/admin/data-table';
import { quyen } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { DialogCRUD } from './dialog-crud';
import * as React from 'react';
import { DialogConfirmDelete } from '@/pages/admin/dialog-confirm-delete';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Quản lý người dùng',
    href: quyen().url,
  },
];

function getData(): Promise<Payment[]> {
  // Fetch data từ API
  return Promise.resolve([
    {
      id: '1',
      amount: 10000000000,
      status: 'done',
      email: 'HUYPHAN@example.com',
    },
    {
      id: '2',
      amount: 232,
      status: 'success',
      email: 'sonxuandi@example.com',
    },
    {
      id: '3',
      amount: 345,
      status: 'waitting',
      email: 'tanphat@example.com',
    },
    {
      id: '4',
      amount: 433,
      status: 'pending',
      email: 'hoangkha@example.com',
    },
    {
      id: '5',
      amount: 787,
      status: 'pending',
      email: 'khoanhan@example.com',
    },
    {
      id: '1',
      amount: 100,
      status: 'done',
      email: 'HUYPHAN@example.com',
    },
    {
      id: '2',
      amount: 232,
      status: 'success',
      email: 'sonxuandi@example.com',
    },
  ]);
}


export default function QuyenPage() {
    const [openDialog, setOpenDialog] = React.useState(false);
    const [dialogMode, setDialogMode] = React.useState<'create' | 'edit'>('create');
    const [selectedRow, setSelectedRow] = React.useState<Payment | null>(null);
    const [openConfirm, setOpenConfirm] = React.useState(false);
    const [rowToDelete, setRowToDelete] = React.useState<Payment | null>(null);

    const [data, setData] = useState<Payment[]>([]);

  useEffect(() => {
    getData().then(setData);
  }, []);

    const handleAdd = () => {
        setDialogMode('create');
        setSelectedRow(null);
        setOpenDialog(true);
    };

    const handleEdit = (row: Payment) => {
        setDialogMode('edit');
        setSelectedRow(row);
        setOpenDialog(true);
    };

    const handleSave = (newRow: Payment) => {
        if (dialogMode === 'edit' && selectedRow) {
            setData((prev) => prev.map((item) => (item.id === newRow.id ? newRow : item)));
        } else {
            setData((prev) => [...prev, { ...newRow, id: Date.now().toString() }]);
        }
    };

    const handleDelete = (row: Payment) => {
        setRowToDelete(row);
        setOpenConfirm(true);
    };

    const confirmDelete = () => {
        if (rowToDelete) {
            setData((prev) => prev.filter((item) => item.id !== rowToDelete.id));
        }
        setRowToDelete(null);
        setOpenConfirm(false);
    };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Quyền" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
          <DataTable columns={columns(handleEdit, handleDelete)} data={data} onAdd={handleAdd} />
      </div>
        <DialogCRUD
            open={openDialog}
            row={selectedRow}
            onClose={() => setOpenDialog(false)}
            onSave={handleSave}
        />

        <DialogConfirmDelete
            open={openConfirm}
            onClose={() => setOpenConfirm(false)}
            onConfirm={confirmDelete}
        />

    </AppLayout>
  );
}
