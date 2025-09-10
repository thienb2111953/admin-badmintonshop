import { Plus, Store } from 'lucide-react';
import TableCard from './data-table';
import { toast } from 'sonner';
import { useState } from 'react';
import ConfirmDialog from './confirm-dialog';
import { ModalDialog } from './modal-dialog';
import { createColumns } from './columns';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý thương hiệu', href: '/thuong_hieu' }];

export interface ThuongHieu {
  id_thuong_hieu: number;
  ma_thuong_hieu: string;
  ten_thuong_hieu: string;
  logo_url: string;
}

export default function ThuongHieuPage({ thuong_hieu }: { thuong_hieu: ThuongHieu[] }) {
  const [selectedId, setSelectedId] = useState<number | null>(null);
  const [openConfirm, setOpenConfirm] = useState(false);
  const [modalOpen, setModalOpen] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const [editData, setEditData] = useState<ThuongHieu | null>(null);

  // Chỉ dùng useForm của Inertia cho việc delete
  // const { delete: destroy, processing: deleteProcessing } = useForm();
  const {
    delete: destroy,
    processing: deleteProcessing,
    setData,
    errors,
  } = useForm({
    id_thuong_hieu: null as number | null,
  });

  const columns = createColumns({
    setSelectedId,
    setOpenConfirm,
    onEdit: (rowData: ThuongHieu) => {
      setEditMode(true);
      setEditData(rowData);
      setModalOpen(true);
    },
  });

  const handleOpenAdd = () => {
    setEditMode(false);
    setEditData(null);
    setModalOpen(true);
  };

  const handleCloseModal = () => {
    setModalOpen(false);
    setEditMode(false);
    setEditData(null);
  };

  const handleDelete = () => {
    if (!selectedId) {
      toast.error('Vui lòng chọn thương hiệu để xóa');
      return;
    }

    router.delete(route('thuong_hieu.destroy', selectedId), {
      onSuccess: () => {
        toast.success('Xóa thành công');
        setOpenConfirm(false);
        setSelectedId(null);
      },
      onError: (errs) => {
        console.log('Delete errors:', errs);
        Object.values(errs).forEach((msg) => toast.error(msg));
      },
    });
  };

  const props = {
    backUrl: '/thong-tin',
    backText: 'Quay lại',
    title: 'Thương hiệu',
    description: 'Quản lý thương hiệu',
    subtitle: 'Danh sách thương hiệu',
    subdescription: 'Hiển thị các thương hiệu',
    icon: Store,
  };

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <TableCard {...props} data={thuong_hieu} columns={columns} isLoading={deleteProcessing}>
        <Button onClick={handleOpenAdd}>
          <Plus />
          <Label>Thêm thương hiệu</Label>
        </Button>

        <ModalDialog
          open={modalOpen}
          onClose={handleCloseModal}
          fields={[
            { key: 'ma_thuong_hieu', label: 'Mã thương hiệu' },
            { key: 'ten_thuong_hieu', label: 'Tên thương hiệu' },
            { key: 'logo_url', label: 'Logo URL' },
          ]}
          title={editMode ? 'Cập nhật thương hiệu' : 'Thêm thương hiệu'}
          description={editMode ? 'Chỉnh sửa thông tin' : 'Nhập thông tin mới'}
          initialValues={
            editData || {
              id_thuong_hieu: 0,
              ma_thuong_hieu: '',
              ten_thuong_hieu: '',
              logo_url: '',
            }
          }
          submitRoute={
            editMode && editData ? route('thuong_hieu.update', editData.id_thuong_hieu) : route('thuong_hieu.store')
          }
          method={editMode ? 'patch' : 'post'}
        />
      </TableCard>

      <ConfirmDialog
        openConfirm={openConfirm}
        setOpenConfirm={setOpenConfirm}
        submitFn={handleDelete}
        title="Bạn có chắc muốn xóa thương hiệu này?"
      />
    </AppLayout>
  );
}
