import { useState } from 'react';
import { DataTable } from '@/components/custom/data-table';
import { Columns } from './columns';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { router, useForm } from '@inertiajs/react';
import { SanPham, AnhSanPham } from '@/types';

export default function AnhSanPhamPage({
  anh_san_phams,
  san_pham_info,
}: {
  anh_san_phams: AnhSanPham[];
  san_pham_info: SanPham;
}) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<AnhSanPham | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const form = useForm<AnhSanPham>({
    id_anh_san_pham: 0,
    id_san_pham_chi_tiet: 0,
    ten_mau: '',
    files_anh_san_pham_new: [],
    path_anh_san_pham_old: [],
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      id_san_pham_chi_tiet: 0,
      ten_mau: '',
      files_anh_san_pham_new: [],
      path_anh_san_pham_old: [],
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: AnhSanPham) => {
    setSelectedRow(row);
    form.setData({
      id_anh_san_pham: row.id_anh_san_pham,
      id_san_pham_chi_tiet: row.id_san_pham_chi_tiet,
      ten_mau: row.ten_mau,
      files_anh_san_pham_new: [],
      path_anh_san_pham_old: row.path_anh_san_pham_old,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: AnhSanPham) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('anh_san_pham.destroy', { id_san_pham: san_pham_info.id_san_pham }), {
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
      router.post(
        route('anh_san_pham.update', { id_san_pham: san_pham_info.id_san_pham }),
        { _method: 'put', ...form.data },
        {
          forceFormData: true,
          onSuccess: () => {
            toast.success('Cập nhật thành công!');
            setOpenDialog(false);
          },
          onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
        },
      );
    } else {
      form.post(route('anh_san_pham.store', { id_san_pham: san_pham_info.id_san_pham }), {
        forceFormData: true,
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
      <DataTable columns={Columns(handleEdit, handleDelete)} data={anh_san_phams} onAdd={handleAdd} />

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
