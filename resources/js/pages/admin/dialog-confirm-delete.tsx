import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";

interface DialogConfirmDeleteProps {
    open: boolean;
    onClose: () => void;
    onConfirm: () => void;
}

export function DialogConfirmDelete({ open, onClose, onConfirm }: DialogConfirmDeleteProps) {
    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Bạn có chắc chắn muốn xóa?</DialogTitle>
                    <DialogDescription>
                        Thao tác này không thể hoàn tác. Dữ liệu sẽ bị xóa vĩnh viễn.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter className="flex justify-end gap-2">
                    <Button variant="outline" onClick={onClose}>
                        Hủy
                    </Button>
                    <Button variant="destructive" onClick={onConfirm}>
                        Xóa
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
