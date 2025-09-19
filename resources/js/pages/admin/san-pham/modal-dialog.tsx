import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { SanPham } from '@/types';
import { Label } from '@/components/ui/label';
import { Combobox } from '@/components/ui/combobox';
import { slugify } from 'transliteration';
import { useEffect } from 'react';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<SanPham>;
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, form, onSubmit, btnTitle }: Props) {
  const { data, setData, errors } = form;

  const handleFormSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit();
  };

  function formatNumber(value: number | null): string {
    if (value == null || isNaN(value)) return '';
    return value.toLocaleString('vi-VN'); // ví dụ: 1000000 -> "1.000.000"
  }

  return (
    <Dialog open={open} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
        </DialogHeader>

        <form onSubmit={handleFormSubmit} className="mt-4 space-y-4">
          <div className="grid gap-4">
            <div className="grid gap-3">
              <Label htmlFor="ma_san_pham">Mã sản phẩm</Label>
              <Input
                id="ma_san_pham"
                placeholder="Mã sản phẩm"
                value={data.ma_san_pham ?? ''}
                onChange={(e) => {
                  const value = e.target.value;
                  setData('ma_san_pham', value);
                }}
              />
              {errors.ma_san_pham && <p className="text-red-500">{errors.ma_san_pham}</p>}
            </div>

            <div className="grid gap-3">
              <Label htmlFor="ten_san_pham">Tên sản phẩm</Label>
              <Input
                id="ten_san_pham"
                placeholder="Tên sản phẩm"
                value={data.ten_san_pham ?? ''}
                onChange={(e) => setData('ten_san_pham', e.target.value)}
              />
              {errors.ten_san_pham && <p className="text-red-500">{errors.ten_san_pham}</p>}
            </div>

            <div className="grid gap-3">
              <Label htmlFor="gia_niem_yet">Giá niêm yết</Label>
              <Input
                id="gia_niem_yet"
                placeholder="Giá niêm yết"
                type="text"
                value={formatNumber(data.gia_niem_yet)}
                onChange={(e) => {
                  const raw = e.target.value.replace(/\D/g, '');
                  setData('gia_niem_yet', raw ? Number(raw) : null);
                }}
              />
              {errors.gia_niem_yet && <p className="text-red-500">{errors.gia_niem_yet}</p>}
            </div>

            <div className="grid gap-3">
              <Label htmlFor="gia_ban">Giá bán</Label>
              <Input
                id="gia_ban"
                placeholder="Giá bán"
                type="text"
                value={formatNumber(data.gia_ban)}
                onChange={(e) => {
                  const raw = e.target.value.replace(/\D/g, '');
                  setData('gia_ban', raw ? Number(raw) : null);
                }}
              />
              {errors.gia_ban && <p className="text-red-500">{errors.gia_ban}</p>}
            </div>

            <div className="grid gap-3">
              <Label htmlFor="mo_ta">Mô tả</Label>
              <Input
                id="mo_ta"
                placeholder="Mô tả"
                value={data.mo_ta ?? ''}
                onChange={(e) => setData('mo_ta', e.target.value)}
              />
              {errors.mo_ta && <p className="text-red-500">{errors.mo_ta}</p>}
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
