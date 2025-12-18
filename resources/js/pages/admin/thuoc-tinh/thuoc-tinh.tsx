import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, ThuocTinh } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { thuoc_tinh } from '@/routes';
import { ThuocTinhForm } from './modal-dialog';
import { useCrudModal } from '@/hooks/use-crud-modal';

export default function ThuocTinhPage({ thuoc_tinhs }: { thuoc_tinhs: ThuocTinh[] }) {
    const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý thuộc tính', href: thuoc_tinh() }];

    const form = useForm<ThuocTinh>({
        id_thuoc_tinh: 0,
        ten_thuoc_tinh: '',
    });

    const crud = useCrudModal<ThuocTinh>(form);

    const handleSubmit = () => {
        const action = crud.selected ? form.put : form.post;

        action(
            crud.selected ? route('thuoc_tinh.update') : route('thuoc_tinh.store'),
            {
                onSuccess: () => {
                    toast.success(crud.selected ? 'Cập nhật thành công!' : 'Tạo mới thành công!');
                    crud.close();
                },
            }
        );
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Quản lý Thuộc tính" />

            <div className="flex flex-col gap-4 p-4">
                <DataTable
                    data={thuoc_tinhs}
                    onAdd={crud.openAdd}
                    columns={columns(
                        (row) =>
                            crud.openEdit(row, (r) => ({
                                id_thuoc_tinh: r.id_thuoc_tinh,
                                ten_thuoc_tinh: r.ten_thuoc_tinh,
                            })),
                        (row) => crud.openDeleteConfirm(row)
                    )}
                />
            </div>

            <ThuocTinhForm
                open={crud.open}
                isEdit={!!crud.selected}
                form={form}
                onClose={crud.close}
                onSubmit={handleSubmit}
            />

            <DialogConfirmDelete
                open={crud.openDelete}
                onClose={crud.closeDeleteConfirm}
                onConfirm={() =>
                    router.delete(route('thuoc_tinh.destroy'), {
                        data: { id_thuoc_tinh: crud.deleteRow?.id_thuoc_tinh },
                        onSuccess: () => {
                            toast.success('Xóa thành công!');
                            crud.closeDeleteConfirm();
                        },
                    })
                }
            />
        </AppLayout>
    );
}
