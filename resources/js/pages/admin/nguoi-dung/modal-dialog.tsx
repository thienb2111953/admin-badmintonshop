import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useState, useEffect } from 'react';
import { useForm, type InertiaFormProps } from '@inertiajs/react';
import { User } from '@/types';
import { Label } from '@/components/ui/label';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { CalendarIcon, ChevronDownIcon } from 'lucide-react';
import { format } from 'date-fns';
interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<User>;
  onSubmit: () => void;
}

export function ModalDialog({ open, onClose, onSubmit, form, title, btnTitle }: Props) {
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
              <Label htmlFor="name">Họ tên</Label>
              <Input
                id="name"
                placeholder="Họ tên"
                value={data.name ?? ''}
                onChange={(e) => setData('name', e.target.value)}
              />
              {errors.name && <p className="text-red-500">{errors.name}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="email">Email</Label>
              <Input
                id="email"
                placeholder="Email"
                value={data.email ?? ''}
                onChange={(e) => setData('email', e.target.value)}
              />
              {errors.email && <p className="text-red-500">{errors.email}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="password">Password</Label>
              <Input
                id="password"
                type="password"
                placeholder="password"
                value={data.password ?? ''}
                onChange={(e) => setData('password', e.target.value)}
              />
              {errors.password && <p className="text-red-500">{errors.password}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="ngay_sinh" className="px-1">
                Ngày sinh
              </Label>
              <Popover open={openDate} onOpenChange={setOpenDate}>
                <PopoverTrigger asChild>
                  <Button variant="outline" id="ngay_sinh" className="w-60 justify-between font-normal">
                    {data.ngay_sinh ? format(new Date(data.ngay_sinh), 'dd/MM/yyyy') : 'Chọn ngày'}
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
                      setData('ngay_sinh', selectedDate.toISOString().slice(0, 10)); // yyyy-MM-dd
                      setOpenDate(false);
                    }}
                  />
                </PopoverContent>
              </Popover>
              {errors.ngay_sinh && <p className="text-red-500">{errors.ngay_sinh}</p>}
            </div>

            <div className="grid gap-3">
              <Label htmlFor="sdt">Số điện thoại</Label>
              <Input
                id="sdt"
                placeholder="Số điện thoại"
                value={data.sdt ?? ''}
                onChange={(e) => setData('sdt', e.target.value)}
              />
              {errors.sdt && <p className="text-red-500">{errors.sdt}</p>}
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
