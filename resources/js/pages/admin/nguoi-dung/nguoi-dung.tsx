import AppLayout from '@/layouts/app-layout';
import { columns } from '@/pages/admin/nguoi-dung/columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, User } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { nguoi_dung } from '@/routes';
import axios from 'axios';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý người dùng', href: nguoi_dung() }];

export default function NguoiDungPage({ users }: { users: User[] }) {
    const [openDialog, setOpenDialog] = useState(false);
    const [selectedRow, setSelectedRow] = useState<User | null>(null);
    const [openConfirm, setOpenConfirm] = useState(false);

    const form = useForm<User>({
        id_nguoi_dung: 0,
        name: '',
        email: '',
        password: '',
        ngay_sinh: '',
        sdt: ''
    });

    const handleAdd = () => {
        setSelectedRow(null);
        form.setData({ id_nguoi_dung: 0, name: '', email: '', password: '', ngay_sinh: '', sdt: '' });
        setOpenDialog(true);
    };

    const handleEdit = (row: User) => {
        setSelectedRow(row);
        form.setData({
            id_nguoi_dung: row.id_nguoi_dung,
            name: row.name,
            email: row.email,
            ngay_sinh: row.ngay_sinh,
            sdt: row.sdt
        });
        setOpenDialog(true);
    };

    const handleDelete = (row: User) => {
        setSelectedRow(row);
        setOpenConfirm(true);
    };

    const confirmDelete = () => {
        router.delete(route('nguoi_dung.destroy'), {
            data: { id_nguoi_dung: selectedRow?.id_nguoi_dung },
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Xóa thành công!');
                setOpenConfirm(false);
            },
            onError: () => toast.error('Xóa thất bại!')
        });
    };

    const handleSubmit = () => {
        if (selectedRow) {
            // Cập nhật
            form.put(route('nguoi_dung.update'), {
                onSuccess: () => {
                    toast.success('Cập nhật thành công!');
                    setOpenDialog(false);
                },
                onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string))
            });
        } else {
            // Thêm mới
            form.post(route('nguoi_dung.store'), {
                onSuccess: () => {
                    toast.success('Tạo mới thành công!');
                    setOpenDialog(false);
                },
                onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string))
            });
        }
    };

    const handlePayment = () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/api/check-out';

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrf) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrf;
            form.appendChild(csrfInput);
        }

        const ids = [54];
        const inputName = 'id_don_hang';

        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `${inputName}[]`;
            input.value = id;
            form.appendChild(input);
        });

        // 👉 Thêm ma_don_hang
        const inputMaDonHang = document.createElement('input');
        inputMaDonHang.type = 'hidden';
        inputMaDonHang.name = 'ma_don_hang';
        inputMaDonHang.value = "DH69233275C0B1C";
        form.appendChild(inputMaDonHang);

        // 👉 Thêm tong_tien
        const inputTongTien = document.createElement('input');
        inputTongTien.type = 'hidden';
        inputTongTien.name = 'tong_tien';
        inputTongTien.value = "4368276";
        form.appendChild(inputTongTien);

        document.body.appendChild(form);
        form.submit();
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Quản lý Quyền" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <DataTable columns={columns(handleEdit, handleDelete)} data={users} onAdd={handleAdd}
                           filters={[
                               { columnId: 'quyen', title: 'Quyền', options: ['Admin', 'User'] },
                           ]}
                />
            </div>

          {/*<button*/}
          {/*  type="button"*/}
          {/*  onClick={handlePayment}*/}
          {/*  className="rounded-md bg-blue-600 px-4 py-2 text-white hover:bg-blue-700"*/}
          {/*>*/}
          {/*  Thanh toán*/}
          {/*</button>*/}

            <ModalDialog
                open={openDialog}
                onClose={() => setOpenDialog(false)}
                onSubmit={handleSubmit}
                form={form}
                title={selectedRow ? 'Cập nhật người dùng' : 'Thêm người dùng'}
                btnTitle={selectedRow ? 'Cập nhật' : 'Thêm'}
            />

            <DialogConfirmDelete open={openConfirm} onClose={() => setOpenConfirm(false)} onConfirm={confirmDelete} />
        </AppLayout>
    );
}
