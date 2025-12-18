// hooks/use-crud-modal.ts
import { useState } from 'react';
import { InertiaFormProps } from '@inertiajs/react';

export function useCrudModal<T extends object>(
    form: InertiaFormProps<T>
) {
    const [open, setOpen] = useState(false);
    const [selected, setSelected] = useState<T | null>(null);

    // delete confirm
    const [openDelete, setOpenDelete] = useState(false);
    const [deleteRow, setDeleteRow] = useState<T | null>(null);

    const clearForm = () => {
        form.reset();
        form.clearErrors();
        setSelected(null);
    };

    const openAdd = () => {
        clearForm();
        setOpen(true);
    };

    const openEdit = (row: T, mapData: (row: T) => Partial<T>) => {
        setSelected(row);
        form.setData(mapData(row));
        setOpen(true);
    };

    const close = () => {
        setOpen(false);
        clearForm();
    };

    // ✅ DELETE
    const openDeleteConfirm = (row: T) => {
        setDeleteRow(row);
        setOpenDelete(true);
    };

    const closeDeleteConfirm = () => {
        setOpenDelete(false);
        setDeleteRow(null);
    };

    return {
        // form modal
        open,
        selected,
        openAdd,
        openEdit,
        close,

        // delete modal
        openDelete,
        deleteRow,
        openDeleteConfirm,
        closeDeleteConfirm,
    };
}
