import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { ThuongHieu } from '@/types';
import { Label } from '@/components/ui/label';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<ThuongHieu>;
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, form, onSubmit, btnTitle }: Props) {
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
              <Label htmlFor="ma_thuong_hieu">Mã thương hiệu</Label>
              <Input
                id="ma_thuong_hieu"
                placeholder="Mã thương hiệu"
                value={data.ma_thuong_hieu ?? ''}
                onChange={(e) => setData('ma_thuong_hieu', e.target.value)}
              />
              {errors.ma_thuong_hieu && <p className="text-red-500">{errors.ma_thuong_hieu}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="ten_thuong_hieu">Tên thương hiệu</Label>
              <Input
                id="ten_thuong_hieu"
                placeholder="Tên thương hiệu"
                value={data.ten_thuong_hieu ?? ''}
                onChange={(e) => setData('ten_thuong_hieu', e.target.value)}
              />
              {errors.ten_thuong_hieu && <p className="text-red-500">{errors.ten_thuong_hieu}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="logo_url">Logo</Label>
              <Input
                id="logo_url"
                placeholder="logo_url"
                type="file"
                onChange={(e) => setData('logo_url', e.target.files ? e.target.files[0] : null)}
              />
              {errors.logo_url && <p className="text-red-500">{errors.logo_url}</p>}
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
