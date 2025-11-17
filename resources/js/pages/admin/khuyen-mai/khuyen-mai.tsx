import { useState } from 'react';
import { DataTable } from '@/components/custom/data-table';
import { Columns } from './columns';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { router, useForm } from '@inertiajs/react';
import { KhuyenMai } from '@/types';

export default function KhuyenMaiPage({
                                          khuyen_mais,
                                      }: {
    khuyen_mais: KhuyenMai[];
}) {
    const [openDialog, setOpenDialog] = useState(false);
    const [selectedRow, setSelectedRow] = useState<KhuyenMai | null>(null);
    const [openConfirm, setOpenConfirm] = useState(false);

    const form = useForm<KhuyenMai>({
        id_khuyen_mai: 0,
        ma_khuyen_mai: '',
        ten_khuyen_mai: '',
        gia_tri: 0,
        don_vi_tinh: 'percent',
        ngay_bat_dau: new Date(),
        ngay_ket_thuc: new Date(),
    });

    const handleAdd = () => {
        setSelectedRow(null);

        form.setData({
            id_khuyen_mai: 0,
            ma_khuyen_mai: '',
            ten_khuyen_mai: '',
            gia_tri: 0,
            don_vi_tinh: 'percent',
            ngay_bat_dau: new Date(),
            ngay_ket_thuc: new Date(),
        });

        setOpenDialog(true);
    };

    const handleEdit = (row: KhuyenMai) => {
        setSelectedRow(row);

        form.setData({
            id_khuyen_mai: row.id_khuyen_mai,
            ma_khuyen_mai: row.ma_khuyen_mai,
            ten_khuyen_mai: row.ten_khuyen_mai,
            gia_tri: row.gia_tri,
            don_vi_tinh: row.don_vi_tinh,
            ngay_bat_dau: row.ngay_bat_dau,
            ngay_ket_thuc: row.ngay_ket_thuc,
        });

        setOpenDialog(true);
    };

    // ====== DELETE ======
    const handleDelete = (row: KhuyenMai) => {
        setSelectedRow(row);
        setOpenConfirm(true);
    };

    const confirmDelete = () => {
        router.delete(route('khuyen_mai.destroy'), {
            data: { id_khuyen_mai: selectedRow?.id_khuyen_mai },
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
            form.put(route('khuyen_mai.update', selectedRow.id_khuyen_mai), {
                onSuccess: () => {
                    toast.success('Cập nhật thành công!');
                    setOpenDialog(false);
                },
                onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
            });
        } else {
            form.post(route('khuyen_mai.store'), {
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
            <DataTable columns={Columns(handleEdit, handleDelete)} data={khuyen_mais} onAdd={handleAdd} />

            <ModalDialog
                open={openDialog}
                onClose={() => setOpenDialog(false)}
                title={selectedRow ? 'Sửa chương trình khuyến mãi' : 'Thêm chương trình khuyến mãi'}
                btnTitle={selectedRow ? 'Cập nhật' : 'Thêm mới'}
                form={form}
                onSubmit={handleSubmit}
            />

            <DialogConfirmDelete
                open={openConfirm}
                onClose={() => setOpenConfirm(false)}
                onConfirm={confirmDelete}
            />
        </>
    );
}
