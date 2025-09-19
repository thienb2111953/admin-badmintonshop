import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, KichThuoc } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { kich_thuoc } from '@/routes';

export default function KichThuocPage({ kich_thuocs }: { kich_thuocs: KichThuoc[] }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<KichThuoc | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý Kích thước', href: kich_thuoc() }];
  const form = useForm<KichThuoc>({
    id_kich_thuoc: 0,
    ten_kich_thuoc: '',
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      ten_kich_thuoc: '',
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: KichThuoc) => {
    setSelectedRow(row);
    form.setData({
      id_kich_thuoc: row.id_kich_thuoc,
      ten_kich_thuoc: row.ten_kich_thuoc,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: KichThuoc) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('kich_thuoc.destroy'), {
      data: { id_kich_thuoc: selectedRow?.id_kich_thuoc },
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
      form.put(route('kich_thuoc.update'), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('kich_thuoc.store'), {
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
        <DataTable columns={columns(handleEdit, handleDelete)} data={kich_thuocs} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa kích thước' : 'Thêm kích thước'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
