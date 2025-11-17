import { useState } from 'react';
import { DataTable } from '@/components/custom/data-table';
import { Columns } from './columns';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { router, useForm } from '@inertiajs/react';
import { KhuyenMai, SanPham, SanPhamKhuyenMai } from '@/types';

export default function SanPhamKhuyenMaiPage({
    khuyen_mais,
    san_phams,
    san_pham_khuyen_mais,
    }: {
    khuyen_mais: KhuyenMai[];
    san_phams: SanPham[];
    san_pham_khuyen_mais: SanPhamKhuyenMai[];
}) {
    const [openDialog, setOpenDialog] = useState(false);
    const [selectedRow, setSelectedRow] = useState<SanPhamKhuyenMai | null>(null);
    const [openConfirm, setOpenConfirm] = useState(false);

    const form = useForm<SanPhamKhuyenMai>({
        id_san_pham_khuyen_mai: 0,
        id_khuyen_mai: 0,
        id_san_pham: 0,
    });

    const handleAdd = () => {
        setSelectedRow(null);

        form.setData({
            id_san_pham_khuyen_mai: 0,
            id_khuyen_mai: 0,
            id_san_pham: 0,
        });

        setOpenDialog(true);
    };

    const handleEdit = (row: SanPhamKhuyenMai) => {
        setSelectedRow(row);

        form.setData({
            id_san_pham_khuyen_mai: row.id_san_pham_khuyen_mai,
            id_khuyen_mai: row.id_khuyen_mai,
            id_san_pham: row.id_san_pham,
        });

        setOpenDialog(true);
    };

    // ====== DELETE ======
    const handleDelete = (row: SanPhamKhuyenMai) => {
        setSelectedRow(row);
        setOpenConfirm(true);
    };

    const confirmDelete = () => {
        router.delete(route('san_pham_khuyen_mai.destroy'), {
            data: { id_san_pham_khuyen_mai: selectedRow?.id_san_pham_khuyen_mai },
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
            form.put(route('san_pham_khuyen_mai.update', selectedRow.id_san_pham_khuyen_mai), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success('Cập nhật thành công!');
                    setOpenDialog(false);
                },
                onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
            });
        } else {
            form.post(route('san_pham_khuyen_mai.store'), {
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
            <DataTable columns={Columns(handleEdit, handleDelete)} data={san_pham_khuyen_mais} onAdd={handleAdd} />

            <ModalDialog
                open={openDialog}
                onClose={() => setOpenDialog(false)}
                title={selectedRow ? 'Sửa sản phẩm khuyến mãi' : 'Thêm sản phẩm khuyến mãi'}
                btnTitle={selectedRow ? 'Cập nhật' : 'Thêm mới'}
                form={form}
                onSubmit={handleSubmit}
                sanPhamOptions={san_phams}
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
