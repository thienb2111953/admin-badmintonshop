import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { type InertiaFormProps } from '@inertiajs/react';
import { KichThuoc, Mau, SanPhamChiTiet } from '@/types';
import { Label } from '@/components/ui/label';
import { useState, useEffect } from 'react';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { CalendarIcon, ChevronDownIcon } from 'lucide-react';
import { format } from 'date-fns';
import { Combobox } from '@/components/ui/combobox';

interface Props {
  open: boolean;
  onClose: () => void;
  title: string;
  btnTitle: string;
  form: InertiaFormProps<SanPhamChiTiet>;
  onSubmit: () => void;
  mauOptions: Mau[];
  kichThuocOptions: KichThuoc[];
}

export function ModalDialog({ open, onClose, title, form, onSubmit, btnTitle, mauOptions, kichThuocOptions }: Props) {
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
              <Label>Màu</Label>
              <Combobox
                options={mauOptions.map((m) => ({
                  value: m.id_mau,
                  label: m.ten_mau,
                }))}
                value={data.id_mau}
                onChange={(val) => setData('id_mau', val as number)}
                placeholder="Chọn Màu..."
                className="w-full"
              />
              {errors.id_mau && <p className="text-red-500">{errors.id_mau}</p>}
            </div>

            <div className="grid gap-3">
              <Label>Kích thước</Label>
              <Combobox
                options={kichThuocOptions.map((kt) => ({
                  value: kt.id_kich_thuoc,
                  label: kt.ten_kich_thuoc,
                }))}
                value={data.id_kich_thuoc}
                onChange={(val) => setData('id_kich_thuoc', val as number)}
                placeholder="Chọn Kích thước..."
                className="w-full"
              />
              {errors.id_kich_thuoc && <p className="text-red-500">{errors.id_kich_thuoc}</p>}
            </div>
            <div className="grid gap-3">
              <Label htmlFor="so_luong_nhap">Số lượng nhập</Label>
              <Input
                id="so_luong_nhap"
                type="number"
                placeholder="Số lượng nhập"
                value={data.so_luong_nhap ?? ''}
                onChange={(e) => setData('so_luong_nhap', Number(e.target.value))}
              />
              {errors.so_luong_nhap && <p className="text-red-500">{errors.so_luong_nhap}</p>}
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
                      setData('ngay_nhap', selectedDate.toISOString().slice(0, 10)); // yyyy-MM-dd
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
