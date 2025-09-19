import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { DanhMuc, DanhMucThuongHieu, ThuongHieu } from '@/types';
import { Label } from '@/components/ui/label';
import { Combobox } from '@/components/ui/combobox';
import { slugify } from 'transliteration';
import { useEffect } from 'react';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<DanhMucThuongHieu>;
  onSubmit: () => void;
  thuongHieuOptions: ThuongHieu[];
  danhMucOptions: DanhMuc[];
}

export function ModalDialog({
  open,
  onClose,
  title,
  form,
  onSubmit,
  btnTitle,
  thuongHieuOptions,
  danhMucOptions,
}: Props) {
  const { data, setData, errors } = form;

  const handleFormSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit();
  };

  // Hàm build tên danh mục thương hiệu
  function updateTenDanhMucThuongHieu(id_danh_muc: number, id_thuong_hieu: number) {
    const danhMuc = danhMucOptions.find((dm) => dm.id_danh_muc === id_danh_muc);
    const thuongHieu = thuongHieuOptions.find((th) => th.id_thuong_hieu === id_thuong_hieu);

    if (danhMuc && thuongHieu) {
      const ten = `${danhMuc.ten_danh_muc} ${thuongHieu.ten_thuong_hieu}`;
      setData('ten_danh_muc_thuong_hieu', ten);
      setData('slug', slugify(ten));
    }
  }

  // Tự động cập nhật khi id_danh_muc hoặc id_thuong_hieu thay đổi
  useEffect(() => {
    if (data.id_danh_muc && data.id_thuong_hieu) {
      updateTenDanhMucThuongHieu(data.id_danh_muc, data.id_thuong_hieu);
    }
  }, [data.id_danh_muc, data.id_thuong_hieu]);

  return (
    <Dialog open={open} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{title}</DialogTitle>
        </DialogHeader>

        <form onSubmit={handleFormSubmit} className="mt-4 space-y-4">
          <div className="grid gap-4">
            {/* Combobox Thương hiệu */}
            <div className="grid gap-3">
              <Label>Thương hiệu</Label>
              <Combobox
                options={thuongHieuOptions.map((th) => ({
                  label: th.ten_thuong_hieu,
                  value: th.id_thuong_hieu,
                }))}
                value={data.id_thuong_hieu}
                onChange={(val) => setData('id_thuong_hieu', val as number)}
                placeholder="Chọn thương hiệu..."
                className="w-full"
              />
              {errors.id_thuong_hieu && <p className="text-red-500">{errors.id_thuong_hieu}</p>}
            </div>

            {/* Combobox Danh mục */}
            <div className="grid gap-3">
              <Label>Danh mục</Label>
              <Combobox
                options={danhMucOptions.map((dm) => ({
                  label: dm.ten_danh_muc,
                  value: dm.id_danh_muc,
                }))}
                value={data.id_danh_muc}
                onChange={(val) => setData('id_danh_muc', val as number)}
                placeholder="Chọn danh mục..."
                className="w-full"
              />
              {errors.id_danh_muc && <p className="text-red-500">{errors.id_danh_muc}</p>}
            </div>

            {/* Input tên danh mục thương hiệu */}
            <div className="grid gap-3">
              <Label htmlFor="ten_danh_muc_thuong_hieu">Tên danh mục thương hiệu</Label>
              <Input
                id="ten_danh_muc_thuong_hieu"
                placeholder="Tên danh mục thương hiệu"
                value={data.ten_danh_muc_thuong_hieu ?? ''}
                onChange={(e) => {
                  const value = e.target.value;
                  setData('ten_danh_muc_thuong_hieu', value);
                  setData('slug', slugify(value));
                }}
              />
              {errors.ten_danh_muc_thuong_hieu && <p className="text-red-500">{errors.ten_danh_muc_thuong_hieu}</p>}
            </div>

            {/* Input slug */}
            <div className="grid gap-3">
              <Label htmlFor="slug">Slug</Label>
              <Input
                id="slug"
                placeholder="Slug"
                value={data.slug ?? ''}
                onChange={(e) => setData('slug', e.target.value)}
              />
              {errors.slug && <p className="text-red-500">{errors.slug}</p>}
            </div>

            {/* Input mô tả */}
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
