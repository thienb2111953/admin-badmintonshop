import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { Banner } from '@/types';
import { Label } from '@/components/ui/label';
interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<Banner>;
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
              <Label htmlFor="file_logo">Banner</Label>
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
                form.data.id_banner !== 0 &&
                (form.data.img_url ? (
                  <img
                    src={`/storage/${form.data.img_url}`}
                    alt="Logo"
                    className="mx-auto mt-2 h-50 w-full rounded object-contain"
                  />
                ) : (
                  <span className="text-gray-400">Chưa chọn Banner</span>
                ))
              }
              {errors.img_url && <p className="text-red-500">{errors.img_url}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="thu_tu">Thứ tự</Label>
              <Input
                id="thu_tu"
                min={1}
                placeholder="Thứ tự"
                value={data.thu_tu ?? ''}
                onChange={(e) => setData('thu_tu', Number(e.target.value))}
              />
              {errors.thu_tu && <p className="text-red-500">{errors.thu_tu}</p>}
            </div>
            <div className="grid gap-3">
            <Label htmlFor="href">href</Label>
            <Input
              id="href"
              placeholder="href"
              value={data.href ?? ''}
              onChange={(e) => setData('href', e.target.value)}
            />
            {errors.href && <p className="text-red-500">{errors.href}</p>}
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
