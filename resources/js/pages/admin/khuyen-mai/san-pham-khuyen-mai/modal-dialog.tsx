import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { SanPhamChiTiet, NhapHangChiTiet, SanPham, KhuyenMai, SanPhamKhuyenMai } from '@/types';
import { Label } from '@/components/ui/label';
import { Combobox } from '@/components/ui/combobox';
import { formatNumber } from '@/utils/helper';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<SanPhamKhuyenMai>;
  sanPhamOptions: SanPham[];
    khuyenMaiOptions: KhuyenMai[];
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, form, onSubmit, btnTitle, sanPhamOptions, khuyenMaiOptions }: Props) {
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
              <Label>Sản phẩm</Label>
              <Combobox
                options={sanPhamOptions.map((spct) => ({
                  value: spct.id_san_pham,
                  label: spct.ten_san_pham,
                }))}
                value={data.id_san_pham}
                onChange={(val) => setData('id_san_pham', val as number)}
                placeholder="Chọn sản phẩm..."
                className="w-full"
              />
              {errors.id_san_pham && <p className="text-red-500">{errors.id_san_pham}</p>}
            </div>

              <div className="grid gap-3">
                  <Label>Khuyến mãi</Label>
                  <Combobox
                      options={khuyenMaiOptions.map((spct) => ({
                          value: spct.id_khuyen_mai,
                          label: spct.ten_khuyen_mai,
                      }))}
                      value={data.id_khuyen_mai}
                      onChange={(val) => setData('id_khuyen_mai', val as number)}
                      placeholder="Chọn chương trình khuyến mãi..."
                      className="w-full"
                  />
                  {errors.id_khuyen_mai && <p className="text-red-500">{errors.id_khuyen_mai}</p>}
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
