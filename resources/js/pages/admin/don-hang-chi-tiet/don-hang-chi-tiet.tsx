import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, DonHang, DonHangChiTiet, SanPhamChiTiet } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { don_hang, don_hang_chi_tiet } from '@/routes';

export default function DonHangChiTietPage({
  don_hang_info,
  don_hang_chi_tiets,
}: {
  don_hang_info: DonHang;
  don_hang_chi_tiets: DonHangChiTiet[];
}) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<DonHangChiTiet | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);
  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Quản lý Đơn hàng', href: don_hang() },
    { title: `${don_hang_info.ma_don_hang}`, href: don_hang_chi_tiet(don_hang_info.id_don_hang) },
  ];

  const form = useForm<DonHangChiTiet>({
    id_don_hang: don_hang_info.id_don_hang,
    id_don_hang_chi_tiet: 0,
    id_san_pham_chi_tiet: 0,
    so_luong: null,
    don_gia: 0,
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      id_don_hang: don_hang_info.id_don_hang,
      id_don_hang_chi_tiet: 0,
      id_san_pham_chi_tiet: 0,
      so_luong: null,
      don_gia: 0,
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: DonHangChiTiet) => {
    setSelectedRow(row);
    form.setData({
      id_don_hang_chi_tiet: row.id_don_hang_chi_tiet,
      id_san_pham_chi_tiet: row.id_san_pham_chi_tiet,
      so_luong: row.so_luong,
      don_gia: row.don_gia,
      id_don_hang: row.id_don_hang,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: DonHangChiTiet) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('don_hang_chi_tiet.destroy', { id_don_hang: don_hang_info.id_don_hang }), {
      data: { id_don_hang_chi_tiet: selectedRow?.id_don_hang_chi_tiet },
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
      form.put(route('don_hang_chi_tiet.update', { id_don_hang: don_hang_info.id_don_hang }), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('don_hang_chi_tiet.store', { id_don_hang: don_hang_info.id_don_hang }), {
        onSuccess: () => {
          toast.success('Tạo mới thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    }
  };

    const handlePrint = () => {
        const iframe = document.createElement('iframe')
        iframe.style.position = 'fixed'
        iframe.style.right = '0'
        iframe.style.bottom = '0'
        iframe.style.width = '0'
        iframe.style.height = '0'
        iframe.style.border = '0'

        iframe.src = `/don-hang/${don_hang_info.id_don_hang}/print`

        iframe.onload = () => {
            iframe.contentWindow?.focus()
            iframe.contentWindow?.print()
        }

        document.body.appendChild(iframe)
    }


    return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Nhập hàng chi tiết" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} data={don_hang_chi_tiets} onAdd={handlePrint} addButtonLabel="in đơn hàng"/>
      </div>

      {/* <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa Nhập hàng chi tiết' : 'Thêm Nhập hàng chi tiết'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
        sanPhamChiTietOptions={san_pham_chi_tiets}
      /> */}

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
