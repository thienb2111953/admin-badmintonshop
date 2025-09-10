// ModalDialog.tsx
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog';

interface FieldConfig<T> {
  key: keyof T;
  label: string;
  placeholder?: string;
  type?: string;
}

interface ModalDialogProps<T> {
  open: boolean;
  onClose: () => void;
  fields: FieldConfig<T>[];
  title: string;
  description?: string;
  initialValues: T;
  submitRoute: string;
  method?: 'post' | 'patch';
}

export function ModalDialog<T extends Record<string, any>>({
  open,
  onClose,
  fields,
  title,
  description,
  initialValues,
  submitRoute,
  method = 'post',
}: ModalDialogProps<T>) {
  const form = useForm<T>(initialValues);

  // Sync form data khi initialValues thay đổi (cho trường hợp edit)
  useEffect(() => {
    if (open) {
      form.setData(initialValues);
    }
  }, [open, initialValues]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    const submitOptions = {
      onSuccess: () => {
        toast.success(method === 'patch' ? 'Cập nhật thành công' : 'Thêm thành công');
        onClose();
        form.reset();
      },
      onError: (errors: Record<string, string>) => {
        Object.values(errors).forEach((msg) => toast.error(msg));
      },
    };

    if (method === 'patch') {
      form.patch(submitRoute, submitOptions);
    } else {
      form.post(submitRoute, submitOptions);
    }
  };

  const handleOpenChange = (isOpen: boolean) => {
    if (!isOpen) {
      onClose();
      form.reset();
      form.clearErrors();
    }
  };

  return (
    <Dialog open={open} onOpenChange={handleOpenChange}>
      <DialogContent className="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
          {description && <DialogDescription>{description}</DialogDescription>}
        </DialogHeader>

        <form onSubmit={handleSubmit} className="space-y-4">
          {fields.map((field) => (
            <div key={String(field.key)} className="space-y-2">
              <Label htmlFor={String(field.key)}>{field.label}</Label>
              <Input
                id={String(field.key)}
                type={field.type || 'text'}
                placeholder={field.placeholder || ''}
                value={String(form.data[field.key] || '')}
                onChange={(e) => form.setData(field.key, e.target.value as T[keyof T])}
              />
              {form.errors[field.key] && <span className="text-sm text-red-500">{form.errors[field.key]}</span>}
            </div>
          ))}

          <DialogFooter>
            <Button type="button" variant="outline" onClick={() => handleOpenChange(false)}>
              Đóng
            </Button>
            <Button type="submit" disabled={form.processing}>
              {form.processing ? 'Đang xử lý...' : 'Lưu'}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  );
}
