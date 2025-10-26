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
import { formatNumber } from '@/utils/helper';



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
                  <Label htmlFor="gia_niem_yet">Giá niêm yết</Label>
                  <Input
                      id="gia_niem_yet"
                      placeholder="Giá niêm yết"
                      type="text"
                      value={formatNumber(data.gia_niem_yet)}
                      onChange={(e) => {
                          const raw = e.target.value.replace(/\D/g, '');
                          setData('gia_niem_yet', raw ? Number(raw) : null);
                      }}
                  />
                  {errors.gia_niem_yet && <p className="text-red-500">{errors.gia_niem_yet}</p>}
              </div>

              <div className="grid gap-3">
                  <Label htmlFor="gia_ban">Giá bán</Label>
                  <Input
                      id="gia_ban"
                      placeholder="Giá bán"
                      type="text"
                      value={formatNumber(data.gia_ban)}
                      onChange={(e) => {
                          const raw = e.target.value.replace(/\D/g, '');
                          setData('gia_ban', raw ? Number(raw) : null);
                      }}
                  />
                  {errors.gia_ban && <p className="text-red-500">{errors.gia_ban}</p>}
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
