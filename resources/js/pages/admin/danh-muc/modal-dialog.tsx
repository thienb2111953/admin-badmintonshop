import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { DanhMuc } from '@/types';
import { Label } from '@/components/ui/label';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<DanhMuc>; // nhận form từ cha
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, onSubmit, form, title, btnTitle }: Props) {
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
          <div className="grid gap-4">
            <div className="grid gap-3">
              <Label htmlFor="ten_danh_muc">Tên danh mục</Label>
              <Input
                id="ten_danh_muc"
                placeholder="Tên danh mục"
                value={data.ten_danh_muc ?? ''}
                onChange={(e) => setData('ten_danh_muc', e.target.value)}
              />
              {errors.ten_danh_muc && <p className="text-red-500">{errors.ten_danh_muc}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="slug">Slug</Label>
              <Input
                id="slug"
                placeholder="slug"
                value={data.slug ?? ''}
                onChange={(e) => setData('slug', e.target.value)}
              />
              {errors.slug && <p className="text-red-500">{errors.slug}</p>}
            </div>
          </div>

          <div className="flex justify-end gap-2 pt-4">
            <Button type="button" variant="outline" onClick={onClose}>
              Hủy
            </Button>
            <Button type="submit">{btnTitle}</Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
