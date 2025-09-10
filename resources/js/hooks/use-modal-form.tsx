// useModalForm.tsx
import { useForm, UseFormReturn } from 'react-hook-form';
import { useEffect, useState } from 'react';

interface UseModalFormProps<T> {
  defaultValues?: Partial<T>;
}

interface UseModalFormReturn<T> {
  open: boolean;
  setOpen: React.Dispatch<React.SetStateAction<boolean>>;
  openAdd: () => void;
  openEdit: (id: number | string, data: T) => void;
  close: () => void;
  editMode: boolean;
  selectedId: number | string | null;
  form: UseFormReturn<T>;
}

export function useModalForm<T>({ defaultValues = {} }: UseModalFormProps<T>): UseModalFormReturn<T> {
  const [open, setOpen] = useState(false);
  const [editMode, setEditMode] = useState(false);
  const [selectedId, setSelectedId] = useState<number | string | null>(null);

  const form = useForm<T>({
    defaultValues: defaultValues as T,
  });

  useEffect(() => {
    if (open) form.reset(defaultValues as T);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open, defaultValues]);

  const openAdd = () => {
    setSelectedId(null);
    setEditMode(false);
    form.reset({} as T);
    setOpen(true);
  };

  const openEdit = (id: number | string, data: T) => {
    setSelectedId(id);
    setEditMode(true);
    form.reset(data);
    setOpen(true);
  };

  const close = () => {
    setOpen(false);
    form.reset({} as T);
    setEditMode(false);
    setSelectedId(null);
  };

  return {
    open,
    setOpen,
    openAdd,
    openEdit,
    close,
    editMode,
    selectedId,
    form,
  };
}
