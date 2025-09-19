import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { KichThuoc } from '@/types';
import { Label } from '@/components/ui/label';
interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<KichThuoc>;
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
              <Label htmlFor="ten_kich_thuoc">Tên kích thước</Label>
              <Input
                id="ten_kich_thuoc"
                placeholder="Tên kích thước"
                value={data.ten_kich_thuoc ?? ''}
                onChange={(e) => setData('ten_kich_thuoc', e.target.value)}
              />
              {errors.ten_kich_thuoc && <p className="text-red-500">{errors.ten_kich_thuoc}</p>}
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
