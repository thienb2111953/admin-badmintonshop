import { useState } from 'react';
import { DataTable } from '@/components/custom/data-table';
import { Columns } from './columns';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { router, useForm } from '@inertiajs/react';
import { SanPham, SanPhamChiTiet } from '@/types';

export default function TonKhoPage({
  san_pham_chi_tiets,
  san_pham_info,
}: {
  san_pham_chi_tiets: SanPhamChiTiet[];
  san_pham_info: SanPham;
}) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<SanPhamChiTiet | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const form = useForm<SanPhamChiTiet>({
    id_san_pham_chi_tiet: 0,
    ten_kich_thuoc: '',
    ten_mau: '',
    so_luong_ton: '',
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      id_san_pham_chi_tiet: 0,
      ten_kich_thuoc: '',
      ten_mau: '',
      so_luong_ton: '',
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: SanPhamChiTiet) => {
    setSelectedRow(row);
    form.setData({
      id_san_pham_chi_tiet: row.id_san_pham_chi_tiet,
      ten_kich_thuoc: row.ten_kich_thuoc,
      ten_mau: row.ten_mau,
      so_luong_ton: row.so_luong_ton,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: SanPhamChiTiet) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('san_pham_chi_tiet.destroy', { id_san_pham: san_pham_info.id_san_pham }), {
      data: { id_san_pham_chi_tiet: selectedRow?.id_san_pham_chi_tiet },
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
      form.put(route('san_pham_chi_tiet.update', { id_san_pham: san_pham_info.id_san_pham }), {
        onSuccess: () => {
          toast.success('Tạo mới thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      form.post(route('san_pham_chi_tiet.store', { id_san_pham: san_pham_info.id_san_pham }), {
        onSuccess: () => {
          toast.success('Tạo mới thành công!');
          setOpenDialog(false);
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    }
  };

  return (
    <>
      <DataTable columns={Columns(handleEdit, handleDelete)} data={san_pham_chi_tiets} onAdd={handleAdd} />

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa Sản phẩm chi tiết' : 'Thêm Sản phẩm chi tiết'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </>
  );
}
