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
  const { data, setData, errors, progress, reset } = form;

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
              <Label htmlFor="file_logo">Logo</Label>
              <Input
                id="file_logo"
                placeholder="file_logo"
                type="file"
                onChange={(e) => {
                  const file = e.target.files ? e.target.files[0] : null;
                  setData('file_logo', file);
                }}
              />
              {/* preview ảnh mới chọn */}
              {data.file_logo instanceof File && (
                <img
                  src={URL.createObjectURL(data.file_logo)}
                  alt="Preview"
                  className="mx-auto mt-2 h-50 w-full rounded object-contain"
                />
              )}

              {/* nếu đang edit mà chưa chọn file mới thì render ảnh cũ */}
              {!(data.file_logo instanceof File) &&
                form.data.id_thuong_hieu !== 0 &&
                (form.data.logo_url ? (
                  <img
                    src={`/storage/${form.data.logo_url}`}
                    alt="Logo"
                    className="mx-auto mt-2 h-50 w-full rounded object-contain"
                  />
                ) : (
                  <span className="text-gray-400">No logo</span>
                ))}
              {progress && (
                <progress value={progress.percentage} max="100">
                  {progress.percentage}%
                </progress>
              )}

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
