import { useState } from 'react';
import { DataTable } from '@/components/custom/data-table';
import { Columns } from './columns';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { router, useForm } from '@inertiajs/react';
import { KichThuoc, Mau, SanPham, SanPhamChiTiet } from '@/types';

export default function TonKhoPage({
  san_pham_chi_tiets,
  san_pham_info,
  maus,
  kich_thuocs,
}: {
  san_pham_chi_tiets: SanPhamChiTiet[];
  san_pham_info: SanPham;
  maus: Mau[];
  kich_thuocs: KichThuoc[];
}) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<SanPhamChiTiet | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const form = useForm<SanPhamChiTiet>({
    id_san_pham_chi_tiet: 0,
    id_mau: 0,
    id_kich_thuoc: 0,
    so_luong_nhap: 0,
    ngay_nhap: '',
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      id_san_pham_chi_tiet: 0,
      ten_kich_thuoc: '',
      ten_mau: '',
      so_luong_nhap: '',
      ngay_nhap: '',
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: SanPhamChiTiet) => {
    setSelectedRow(row);
    form.setData({
      id_san_pham_chi_tiet: row.id_san_pham_chi_tiet,
      id_mau: row.id_mau,
      id_kich_thuoc: row.id_kich_thuoc,
      so_luong_nhap: row.kho?.[0]?.so_luong_nhap ?? 0,
      ngay_nhap: row.kho?.[0]?.ngay_nhap,
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
        mauOptions={maus}
        kichThuocOptions={kich_thuocs}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </>
  );
}
