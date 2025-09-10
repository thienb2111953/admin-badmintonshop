import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useEffect } from 'react';
import { useForm, type InertiaFormProps } from '@inertiajs/react';
import { Quyen } from '@/types';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  form: InertiaFormProps<Quyen>; 
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, form, onSubmit }: Props) {
  const { data, setData, errors } = form;

  const handleFormSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit();
  };

  return (
    <Dialog open={open} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
        </DialogHeader>

        <form onSubmit={handleFormSubmit} className="mt-4 space-y-4">
          <Input
            placeholder="Tên quyền"
            value={data.ten_quyen}
            onChange={(e) => setData('ten_quyen', e.target.value)}
          />
          {errors.ten_quyen && <p className="text-red-500">{errors.ten_quyen}</p>}

          <div className="flex justify-end gap-2 pt-4">
            <Button type="button" variant="outline" onClick={onClose}>
              Hủy
            </Button>
            <Button type="submit">{data.id_quyen ? 'Cập nhật' : 'Thêm mới'}</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
