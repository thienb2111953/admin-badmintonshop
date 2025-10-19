import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, DonHang } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { don_hang } from '@/routes';

export default function DonHangPage({ don_hangs }: { don_hangs: DonHang[] }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<DonHang | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý Đơn hàng', href: don_hang() }];
  const form = useForm<DonHang>({
    id_don_hang: 0,
    ma_don_hang: '',
    id_nguoi_dung: 1,
    trang_thai_don_hang: '',
    phuong_thuc_thanh_toan: '',
    trang_thai_thanh_toan: '',
    ngay_dat_hang: new Date(),
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      id_don_hang: 0,
      ma_don_hang: '',
      id_nguoi_dung: 1,
      trang_thai_don_hang: '',
      phuong_thuc_thanh_toan: '',
      trang_thai_thanh_toan: '',
      ngay_dat_hang: new Date(),
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: DonHang) => {
    setSelectedRow(row);
    form.setData({
      id_don_hang: row.id_don_hang,
      ma_don_hang: row.ma_don_hang,
      id_nguoi_dung: row.id_nguoi_dung,
      trang_thai_don_hang: row.trang_thai_don_hang,
      phuong_thuc_thanh_toan: row.phuong_thuc_thanh_toan,
      trang_thai_thanh_toan: row.trang_thai_thanh_toan,
      ngay_dat_hang: row.ngay_dat_hang,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: DonHang) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('don_hang.destroy'), {
      data: { id_don_hang: selectedRow?.id_don_hang },
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
      form.patch(route('don_hang.updateTrangThai'), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('don_hang.store'), {
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
      <Head title="Quản lý Đơn hàng" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={don_hangs} onAdd={handleAdd} showAddButton={false}/>
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa Đơn hàng' : 'Thêm Đơn hàng'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
