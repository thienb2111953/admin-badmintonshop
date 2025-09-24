import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { CaiDat } from '@/types';
import { Label } from '@/components/ui/label';
interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<CaiDat>;
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
              <Label htmlFor="ten_cai_dat">Tên cài đặt</Label>
              <Input
                id="ten_cai_dat"
                placeholder="Tên cài đặt"
                value={data.ten_cai_dat ?? ''}
                onChange={(e) => setData('ten_cai_dat', e.target.value)}
              />
              {errors.ten_cai_dat && <p className="text-red-500">{errors.ten_cai_dat}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="gia_tri">Giá trị</Label>
              <Input
                id="gia_tri"
                placeholder="Giá trị"
                value={data.gia_tri ?? ''}
                onChange={(e) => setData('gia_tri', e.target.value)}
              />
              {errors.gia_tri && <p className="text-red-500">{errors.gia_tri}</p>}
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
