import { useState } from 'react';
import { DataTable } from '@/components/custom/data-table';
import { Columns } from './columns';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { router, useForm } from '@inertiajs/react';
import { KhuyenMai, DonHangKhuyenMai} from '@/types';

export default function DonHangKhuyenMaiPage({
    khuyen_mais,
    don_hang_khuyen_mais,
    }: {
    khuyen_mais: KhuyenMai[];
    don_hang_khuyen_mais: DonHangKhuyenMai[];
}) {
    const [openDialog, setOpenDialog] = useState(false);
    const [selectedRow, setSelectedRow] = useState<DonHangKhuyenMai | null>(null);
    const [openConfirm, setOpenConfirm] = useState(false);

    const form = useForm<DonHangKhuyenMai>({
        id_don_hang_khuyen_mai: 0,
        id_khuyen_mai: 0,
        gia_tri_duoc_giam: 0,
    });

    const handleAdd = () => {
        setSelectedRow(null);

        form.setData({
            id_don_hang_khuyen_mai: 0,
            id_khuyen_mai: 0,
            gia_tri_duoc_giam: 0,
        });

        setOpenDialog(true);
    };

    const handleEdit = (row: DonHangKhuyenMai) => {
        setSelectedRow(row);

        form.setData({
            id_don_hang_khuyen_mai: row.id_don_hang_khuyen_mai,
            id_khuyen_mai: row.id_khuyen_mai,
            gia_tri_duoc_giam: row.gia_tri_duoc_giam,
        });

        setOpenDialog(true);
    };

    // ====== DELETE ======
    const handleDelete = (row: DonHangKhuyenMai) => {
        setSelectedRow(row);
        setOpenConfirm(true);
    };

    const confirmDelete = () => {
        router.delete(route('don_hang_khuyen_mai.destroy'), {
            data: { id_don_hang_khuyen_mai: selectedRow?.id_don_hang_khuyen_mai },
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Xóa thành công!');
                setOpenConfirm(false);
            },
            onError: () => toast.error('Xóa thất bại!'),
        });
    };

    // ====== SUBMIT ======
    const handleSubmit = () => {
        if (selectedRow) {
            form.put(route('don_hang_khuyen_mai.update', selectedRow.id_don_hang_khuyen_mai), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Cập nhật thành công!');
                    setOpenDialog(false);
                },
                onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
            });
        } else {
            form.post(route('don_hang_khuyen_mai.store'), {
                preserveScroll: true,
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
            <DataTable columns={Columns(handleEdit, handleDelete)} data={don_hang_khuyen_mais} onAdd={handleAdd} />

            <ModalDialog
                open={openDialog}
                onClose={() => setOpenDialog(false)}
                title={selectedRow ? 'Sửa đơn hàng khuyến mãi' : 'Thêm đơn hàng khuyến mãi'}
                btnTitle={selectedRow ? 'Cập nhật' : 'Thêm mới'}
                form={form}
                onSubmit={handleSubmit}
                khuyenMaiOptions={khuyen_mais}
            />

            <DialogConfirmDelete
                open={openConfirm}
                onClose={() => setOpenConfirm(false)}
                onConfirm={confirmDelete}
            />
        </>
    );
}
