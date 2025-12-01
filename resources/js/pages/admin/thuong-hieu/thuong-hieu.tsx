import AppLayout from '@/layouts/app-layout';
import { columns, sortThuongHieuByLogo } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, ThuongHieu } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { thuong_hieu } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý Thương hiệu', href: thuong_hieu() }];

export default function ThuongHieuPage({ thuong_hieus }: { thuong_hieus: ThuongHieu[] }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<ThuongHieu | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  // Lưu dữ liệu bảng vào state để sắp xếp logo lên đầu
  const [tableData, setTableData] = useState<ThuongHieu[]>(sortThuongHieuByLogo(thuong_hieus));

  useEffect(() => {
    setTableData(sortThuongHieuByLogo(thuong_hieus));
  }, [thuong_hieus]);

  const form = useForm<ThuongHieu>({
    id_thuong_hieu: 0,
    ten_thuong_hieu: '',
    logo_url: null,
    file_logo: null,
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      id_thuong_hieu: 0,
      ten_thuong_hieu: '',
      logo_url: null,
      file_logo: null,
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: ThuongHieu) => {
    setSelectedRow(row);
    form.setData({
      id_thuong_hieu: row.id_thuong_hieu,
      ten_thuong_hieu: row.ten_thuong_hieu,
      logo_url: row.logo_url,
      file_logo: null,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: ThuongHieu) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('thuong_hieu.destroy'), {
      data: { id_thuong_hieu: selectedRow?.id_thuong_hieu },
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
        route('thuong_hieu.update'),
        {
          _method: 'put',
          ...form.data,
        },
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
      form.post(route('thuong_hieu.store'), {
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
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Thương hiệu" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable
          columns={columns(handleEdit, handleDelete)}
          data={tableData} // dùng dữ liệu đã sắp xếp
          onAdd={handleAdd}
        />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa thương hiệu' : 'Thêm thương hiệu'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete
        open={openConfirm}
        onClose={() => setOpenConfirm(false)}
        onConfirm={confirmDelete}
      />
    </AppLayout>
  );
}
