import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { NhapHang } from '@/types';
import { Label } from '@/components/ui/label';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { CalendarIcon, ChevronDownIcon } from 'lucide-react';
import { format } from 'date-fns';
import { useState, useEffect } from 'react';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<NhapHang>;
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, form, onSubmit, btnTitle }: Props) {
  const { data, setData, errors } = form;
  const [date, setDate] = useState<Date | undefined>(undefined);
  const [openDate, setOpenDate] = useState(false);

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
              <Label htmlFor="ma_nhap_hang">Mã nhập hàng</Label>
              <Input
                id="ma_nhap_hang"
                placeholder="Mã nhập hàng"
                value={data.ma_nhap_hang ?? ''}
                onChange={(e) => setData('ma_nhap_hang', e.target.value)}
              />
              {errors.ma_nhap_hang && <p className="text-red-500">{errors.ma_nhap_hang}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="ngay_nhap" className="px-1">
                Ngày nhập
              </Label>
              <Popover open={openDate} onOpenChange={setOpenDate}>
                <PopoverTrigger asChild>
                  <Button variant="outline" id="ngay_nhap" className="w-60 justify-between font-normal">
                    {data.ngay_nhap ? format(new Date(data.ngay_nhap), 'dd/MM/yyyy') : 'Chọn ngày'}
                    <CalendarIcon className="ml-2 h-4 w-4 opacity-50" />
                  </Button>
                </PopoverTrigger>
                <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                  <Calendar
                    className="w-[250px]"
                    mode="single"
                    selected={date}
                    captionLayout="dropdown"
                    onSelect={(selectedDate) => {
                      if (!selectedDate) return;
                      setDate(selectedDate);
                      setData('ngay_nhap', format(selectedDate, 'yyyy-MM-dd'));
                      setOpenDate(false);
                    }}
                  />
                </PopoverContent>
              </Popover>
              {errors.ngay_nhap && <p className="text-red-500">{errors.ngay_nhap}</p>}
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
