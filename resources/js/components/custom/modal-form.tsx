import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { ReactNode } from 'react';

interface ModalFormProps {
    open: boolean;
    title: string;
    submitText: string;
    onClose: () => void;
    onSubmit: () => void;
    children: ReactNode;
}

export function ModalForm({
                              open,
                              title,
                              submitText,
                              onClose,
                              onSubmit,
                              children,
                          }: ModalFormProps) {
    return (
        <Dialog
            open={open}
            onOpenChange={(isOpen) => {
                if (!isOpen) onClose();
            }}
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{title}</DialogTitle>
                </DialogHeader>

                <form
                    onSubmit={(e) => {
                        e.preventDefault();
                        onSubmit();
                    }}
                    className="mt-4 space-y-4"
                >
                    {children}

                    <div className="flex justify-end gap-2 pt-4">
                        <Button type="button" variant="outline" onClick={onClose}>
                            Hủy
                        </Button>
                        <Button type="submit">{submitText}</Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
