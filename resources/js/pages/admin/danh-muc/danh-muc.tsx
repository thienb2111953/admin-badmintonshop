import { Plus, Store } from 'lucide-react';
import TableCard from './data-table';
import { toast } from 'sonner';
import { useEffect, useState } from 'react';
import ConfirmDialog from './confirm-dialog';
import { ModalDialog } from './modal-dialog';
import { createColumns } from './columns';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý danh mục', href: '/danh_muc' }];

interface DanhMuc {
  id_danh_muc: number;
  ten_danh_muc: string;
  slug: string;
}

interface Props {
  danh_mucs: DanhMuc[];
  flash?: {
    success?: string;
    error?: string;
  };
}

export default function pageDanhMuc({ danh_mucs, flash }: Props) {
  const [isOpen, setIsOpen] = useState(false);
  const [editingDanhMuc, setEditingDanhMuc] = useState<DanhMuc | null>(null);
  const [showToast, setShowToast] = useState(flase);
  const [toastMessage, setToastMessage] = useState('');
  const [toastType, setToastType] = useState<'success' | 'error'>('success');

  useEffect(() => {
    if (flash?.success) {
      setToastMessage(flash.success);
      setToastType('success');
      setShowToast(true);
    } else if (flash?.error) {
      setToastMessage(flash.error);
      setToastType('error');
      setShowToast(true);
    }
  }, [flash]);

  useEffect(() => {
    if (showToast) {
      const timer = setTimeout(() => {
        setShowToast(false);
      }, 3000);
      return () => clearTimeout(timer);
    }
  }, [showToast]);

  const {
    data,
    setData,
    post,
    put,
    processing,
    reset,
    delete: destroy,
  } = useForm({
    ten_danh_muc: '',
    slug: '',
  });

  const handleSubmit = (e: React.FormEvent<HTMLFormElement>){
    e.preventDefault();
    if(editingDanhMuc) {
        put(route('danh_muc.update', editingDanhMuc.id_danh_muc), {
            onSuccess: () => {
                setIsOpen(false);
                reset();
                setEditingDanhMuc(null);
            }
        })
    } else {
        post(route('danh_muc.store'), {
            onSuccess: () => {
                setIsOpen(false);
                reset();
            }
        })
    }
  }

    const handleEdit = (danh_muc: DanhMuc) => {
        setEditingDanhMuc(danh_muc);
        setData({
            ten_danh_muc: danh_muc.ten_danh_muc,
            slug: danh_muc.slug || '',
        });
        setIsOpen(true)
    }

    const handleDelete = (id_danh_muc: Number) => {
        destroy(route('danh_muc.destroy', id_danh_muc))
    }

    return  (
        <AppLayout breadcrumbs={breadcrumbs}>
          <Head title="Quản lý Danh mục" />
    
          <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <DataTable
              columns={columns(handleEdit, handleDelete)}
              data={quyen}
              onAdd={handleAdd}
              onDeleteSelected={handleDeleteSelected}
            />
          </div>
    
          <DialogCreateUpdate
            open={openDialog}
            onClose={() => setOpenDialog(false)}
            title={selectedRow ? 'Sửa quyền' : 'Thêm quyền'}
            form={form}
            onSubmit={handleSubmit}
          />
    
          <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
        </AppLayout>
      );

}
