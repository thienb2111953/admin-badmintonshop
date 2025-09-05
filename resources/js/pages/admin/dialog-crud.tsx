import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Payment } from "./columns";
import { useEffect, useState } from "react";

interface Props {
    open: boolean;
    onClose: () => void;
    onSave: (data: Payment) => void;
    row?: Payment | null;
}

export function DialogCRUD({ open, onClose, onSave, row }: Props) {
    const [form, setForm] = useState<Payment>({
        id: "",
        email: "",
        amount: 0,
        status: "pending",
    });

    useEffect(() => {
        if (row) {
            setForm(row);
        } else {
            setForm({ id: "", email: "", amount: 0, status: "pending" });
        }
    }, [row]);

    const handleSubmit = () => {
        onSave(form);
        onClose();
    };

    return (
        <Dialog open={open} onOpenChange={onClose}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{row ? "Sửa người dùng" : "Thêm người dùng"}</DialogTitle>
                </DialogHeader>

                <div className="space-y-4">
                    <Input
                        placeholder="Email"
                        value={form.email}
                        onChange={(e) => setForm({ ...form, email: e.target.value })}
                    />
                    <Input
                        placeholder="Số tiền"
                        type="number"
                        value={form.amount}
                        onChange={(e) => setForm({ ...form, amount: parseFloat(e.target.value) })}
                    />
                    <Input
                        placeholder="Trạng thái"
                        value={form.status}
                        onChange={(e) => setForm({ ...form, status: e.target.value as any })}
                    />
                </div>

                <div className="flex justify-end gap-2 pt-4">
                    <Button variant="outline" onClick={onClose}>
                        Hủy
                    </Button>
                    <Button onClick={handleSubmit}>
                        {row ? "Cập nhật" : "Thêm mới"}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    );
}
