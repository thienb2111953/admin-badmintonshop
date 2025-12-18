import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ModalForm } from '@/components/custom/modal-form';
import { InertiaFormProps } from '@inertiajs/react';
import { ThuocTinh } from '@/types';

interface Props {
    open: boolean;
    isEdit: boolean;
    form: InertiaFormProps<ThuocTinh>;
    onClose: () => void;
    onSubmit: () => void;
}

export function ThuocTinhForm({ open, isEdit, form, onClose, onSubmit }: Props) {
    return (
        <ModalForm
            open={open}
            title={isEdit ? 'Sửa thuộc tính' : 'Thêm thuộc tính'}
            submitText={isEdit ? 'Sửa' : 'Thêm'}
            onClose={onClose}
            onSubmit={onSubmit}
        >
            <div className="grid gap-3">
                <Label htmlFor="ten_thuoc_tinh">Tên thuộc tính</Label>
                <Input
                    id="ten_thuoc_tinh"
                    placeholder={"Nhập tên thuộc tính"}
                    value={form.data.ten_thuoc_tinh}
                    onChange={(e) => form.setData('ten_thuoc_tinh', e.target.value)}
                />
                {form.errors.ten_thuoc_tinh && (
                    <p className="text-sm text-red-500">{form.errors.ten_thuoc_tinh}</p>
                )}
            </div>
        </ModalForm>
    );
}
