//confirm-dialog.tsx
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog';

interface ConfirmDialogProps {
  openConfirm: boolean;
  setOpenConfirm: (open: boolean) => void;
  submitFn: () => void;
  title?: string;
  description?: string;
}

export default function ConfirmDialog({
  openConfirm,
  setOpenConfirm,
  submitFn,
  title,
  description,
}: ConfirmDialogProps) {
  return (
    <AlertDialog open={openConfirm} onOpenChange={setOpenConfirm}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>{title || 'Bạn có muốn thực hiện hành động này không?'}</AlertDialogTitle>
          <AlertDialogDescription>
            {description || 'Hành động này sẽ không thể hoàn tác. Bạn có chắc chắn muốn tiếp tục?'}
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Hủy</AlertDialogCancel>
          <AlertDialogAction onClick={submitFn}>Xác nhận</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}
