import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { SanPhamChiTiet, NhapHangChiTiet } from '@/types';
import { Label } from '@/components/ui/label';
import { Combobox } from '@/components/ui/combobox';
import { formatNumber } from '@/utils/helper';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<NhapHangChiTiet>;
  sanPhamChiTietOptions: SanPhamChiTiet[];
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, form, onSubmit, btnTitle, sanPhamChiTietOptions }: Props) {
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
              <Label>Sản phẩm chi tiết</Label>
              <Combobox
                options={sanPhamChiTietOptions.map((spct) => ({
                  value: spct.id_san_pham_chi_tiet,
                  label: spct.ten_san_pham_chi_tiet,
                }))}
                value={data.id_san_pham_chi_tiet}
                onChange={(val) => setData('id_san_pham_chi_tiet', val as number)}
                placeholder="Chọn sản phẩm..."
                className="w-full"
              />
              {errors.id_san_pham_chi_tiet && <p className="text-red-500">{errors.id_san_pham_chi_tiet}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="don_gia">Đơn giá</Label>
              <Input
                id="don_gia"
                placeholder="Đơn giá"
                type="text"
                value={formatNumber(data.don_gia)}
                onChange={(e) => {
                  const raw = e.target.value.replace(/\D/g, '');
                  setData('don_gia', raw ? Number(raw) : 0);
                }}
              />
              {errors.don_gia && <p className="text-red-500">{errors.don_gia}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="so_luong">Số lượng</Label>
              <Input
                id="so_luong"
                type="number"
                placeholder="Số lượng"
                value={data.so_luong ?? null}
                onChange={(e) => setData('so_luong', Number(e.target.value))}
              />
              {errors.so_luong && <p className="text-red-500">{errors.so_luong}</p>}
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
