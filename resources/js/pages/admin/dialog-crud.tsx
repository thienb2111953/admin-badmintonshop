import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Quyen } from '@/types';
import { useEffect } from 'react';
import { useForm, router } from '@inertiajs/react';
import { toast } from 'sonner';

interface Props {
  open: boolean;
  onClose: () => void;
  row?: Quyen | null;
}

export function DialogCRUD({ open, onClose, row }: Props) {
  const { data, setData, post, processing, errors, reset } = useForm<Quyen>({
    id_quyen: row?.id_quyen ?? 0,
    ten_quyen: row?.ten_quyen ?? '',
  });

  useEffect(() => {
    if (row) {
      setData({
        id_quyen: row.id_quyen,
        ten_quyen: row.ten_quyen,
      });
    } else {
      setData({
        id_quyen: 0,
        ten_quyen: '',
      });
    }
  }, [row]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post('/quyen/store-or-update', {
      onSuccess: () => {
        // load lại danh sách bằng Inertia
        toast.success('Thành công!', {
          description: 'Lưu quyền thành công!',
          // action: {
          //   label: 'Undo',
          //   onClick: () => console.log('Undo'),
          // },
        });
        router.reload({ only: ['quyen'] });
        onClose();
      },
      onError: () => {
        Object.values(errors).forEach((err) => {
          toast.error('Thất bại !!!', {
            description: err as string,
          });
        });
      },
      onFinish: () => {},
    });
  };

  return (
    <Dialog open={open} onOpenChange={onClose}>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>{row ? 'Sửa quyền' : 'Thêm quyền'}</DialogTitle>
        </DialogHeader>

        <form onSubmit={handleSubmit} className="space-y-4">
          <Input placeholder="Quyền" value={data.ten_quyen} onChange={(e) => setData('ten_quyen', e.target.value)} />
          {errors.ten_quyen && <p className="text-red-500">{errors.ten_quyen}</p>}

          <div className="flex justify-end gap-2 pt-4">
            <Button type="button" variant="outline" onClick={onClose}>
              Hủy
            </Button>
            <Button type="submit" disabled={processing}>
              {row ? 'Cập nhật' : 'Thêm mới'}
            </Button>
          </div>
        </form>
      </DialogContent>
    </Dialog>
  );
}
