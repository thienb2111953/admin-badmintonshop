import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { DonHang } from '@/types';
import { Combobox } from '@/components/ui/combobox';
import { Label } from '@/components/ui/label';


interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<DonHang>;
  onSubmit: () => void;
}

const trangThaiList = [
  { label: 'Đang xử lý', value: 'Đang xử lý' },
  { label: 'Vận chuyển', value: 'Vận chuyển' },
  { label: 'Đã nhận', value: 'Đã nhận' },
  { label: 'Hủy', value: 'Hủy' },
]

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
              <Label htmlFor="ma_don_hang">Mã đơn hàng</Label>
              <Input
                id="ma_don_hang"
                disabled
                placeholder="Mã đơn hàng"
                value={data.ma_don_hang ?? ''}
                onChange={(e) => setData('ma_don_hang', e.target.value)}
              />
              {errors.ma_don_hang && <p className="text-red-500">{errors.ma_don_hang}</p>}
            </div>

            <div className="grid gap-3">
              <Label>Trạng thái thương hiệu</Label>
              <Combobox
                options={trangThaiList}
                value={data.trang_thai_don_hang}
                onChange={(val) => setData('trang_thai_don_hang', val as string)}
                placeholder="Chọn Trạng thái thương hiệu..."
                className="w-full"
              />
              {errors.trang_thai_don_hang && <p className="text-red-500">{errors.trang_thai_don_hang}</p>}
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
