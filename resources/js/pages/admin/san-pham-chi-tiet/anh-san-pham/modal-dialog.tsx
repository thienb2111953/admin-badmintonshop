import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { AnhSanPham } from '@/types';
import { Label } from '@/components/ui/label';
import { useState, useEffect } from 'react';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<AnhSanPham>;
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, form, onSubmit, btnTitle }: Props) {
  const { data, setData, errors } = form;
  const [previewFiles, setPreviewFiles] = useState<File[]>([]);

  useEffect(() => {
    if (!open) {
      setPreviewFiles([]);
    }
  }, [open]);

  const handleFormSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit();
  };

  const handleRemoveFile = (index: number) => {
    const newFiles = previewFiles.filter((_, i) => i !== index);
    setPreviewFiles(newFiles);
    setData('files_anh_san_pham_new', newFiles);
  };

  const handleRemoveUrl = (index: number) => {
    const newUrls = (data.path_anh_san_pham_old ?? []).filter((_, i) => i !== index);
    setData('path_anh_san_pham_old', newUrls);
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
              <Label htmlFor="ten_mau">Màu</Label>
              <Input
                id="ten_mau"
                placeholder="Màu"
                disabled
                value={data.ten_mau ?? ''}
                onChange={(e) => setData('ten_mau', e.target.value)}
              />
              {errors.ten_mau && <p className="text-red-500">{errors.ten_mau}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="files">Ảnh sản phẩm</Label>
              <Input
                id="files"
                type="file"
                multiple
                accept="image/*"
                onChange={(e) => {
                  if (e.target.files) {
                    const files = Array.from(e.target.files).map((f) => ({
                      file: f,
                      thu_tu: '',
                    }));
                    setData('files_anh_san_pham_new', files);
                    setPreviewFiles(files);
                  }
                }}
              />


                {/* Preview ảnh cũ từ DB */}
                {data.path_anh_san_pham_old?.length > 0 && (
                    <div className="mt-2 grid gap-2 max-h-72 overflow-y-auto pr-2">
                        {data.path_anh_san_pham_old.map((file, index) => (
                            <div key={index} className="flex items-center gap-3 rounded-md border p-2">
                                <img
                                    src={`/storage/${file.anh_url ?? file}`}
                                    alt={`anh-${index}`}
                                    className="h-16 w-16 rounded-md border object-cover"
                                />

                                <div className="min-w-0 flex-1 space-y-1">
                                    <p className="text-sm font-medium text-gray-700">Ảnh đã lưu</p>

                                    <Input
                                        type="number"
                                        placeholder="Thứ tự"
                                        value={file.thu_tu ?? ''}
                                        onChange={(e) => {
                                            const newUrls = [...data.path_anh_san_pham_old];
                                            newUrls[index] = { ...file, thu_tu: e.target.value };
                                            setData('path_anh_san_pham_old', newUrls);
                                        }}
                                        className="h-8 w-24"
                                    />
                                </div>

                                <Button type="button" size="sm" variant="outline" onClick={() => handleRemoveUrl(index)}>
                                    Xóa
                                </Button>
                            </div>
                        ))}
                    </div>
                )}

                {/* Preview file mới upload */}
                {previewFiles.length > 0 && (
                    <div className="mt-2 grid gap-2 max-h-72 overflow-y-auto pr-2">
                        {previewFiles.map((item, index) => (
                            <div key={index} className="flex items-center gap-3 rounded-md border p-2">
                                <img
                                    src={URL.createObjectURL(item.file)}
                                    alt={item.file.name}
                                    className="h-16 w-16 rounded-md border object-cover"
                                />

                                <div className="min-w-0 flex-1 space-y-1">
                                    <p className="max-w-[300px] truncate text-sm font-medium">{item.file.name}</p>
                                    <p className="text-xs text-gray-500">{(item.file.size / 1024).toFixed(1)} KB</p>

                                    <Input
                                        type="number"
                                        placeholder="Thứ tự"
                                        value={item.thu_tu}
                                        onChange={(e) => {
                                            const newFiles = [...previewFiles];
                                            newFiles[index] = { ...item, thu_tu: e.target.value };
                                            setPreviewFiles(newFiles);
                                            setData('files_anh_san_pham_new', newFiles);
                                        }}
                                        className="h-8 w-24"
                                    />
                                </div>

                                <Button type="button" size="sm" variant="outline" onClick={() => handleRemoveFile(index)}>
                                    Xóa
                                </Button>
                            </div>
                        ))}
                    </div>
                )}

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
