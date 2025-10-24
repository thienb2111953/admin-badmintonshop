import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { Banner, type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { banner } from '@/routes';

export default function BannerPage({ banners }: { banners: Banner[] }) {
  const [openDialog, setOpenDialog] = useState(false);
  const [selectedRow, setSelectedRow] = useState<Banner | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);

  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý Banner', href: banner() }];
  const form = useForm<Banner>({
    id_banner: 0,
    img_url: '',
    thu_tu: 1,
    href: '',
    logo_url: null,
    file_logo: null,
  });

  const handleAdd = () => {
    setSelectedRow(null);
    form.setData({
      img_url: '',
      thu_tu: 1,
      href: '',
      logo_url: null,
      file_logo: null,
    });
    setOpenDialog(true);
  };

  const handleEdit = (row: Banner) => {
    setSelectedRow(row);
    form.setData({
      id_banner: row.id_banner,
      img_url: row.img_url,
      thu_tu: row.thu_tu,
      href: row.href,
      file_logo: null,
    });
    setOpenDialog(true);
  };

  const handleDelete = (row: Banner) => {
    setSelectedRow(row);
    setOpenConfirm(true);
  };

  const confirmDelete = () => {
    router.delete(route('banner.destroy'), {
      data: { id_banner: selectedRow?.id_banner },
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
        route('banner.update'),
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
      form.post(route('banner.store'), {
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
      <Head title="Quản lý Thuộc tính" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns(handleEdit, handleDelete)} disableSearchBox={true} data={banners} onAdd={handleAdd} />
      </div>

      <ModalDialog
        open={openDialog}
        onClose={() => setOpenDialog(false)}
        title={selectedRow ? 'Sửa banner' : 'Thêm banner'}
        btnTitle={selectedRow ? 'Sửa' : 'Thêm'}
        form={form}
        onSubmit={handleSubmit}
      />

      <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
    </AppLayout>
  );
}
