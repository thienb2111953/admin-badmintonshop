import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { InertiaFormProps } from '@inertiajs/react';
import { KhuyenMai } from '@/types';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverTrigger, PopoverContent } from '@/components/ui/popover';
import { CalendarIcon } from 'lucide-react';
import { format } from 'date-fns';
import { Combobox } from '@/components/ui/combobox';
import { useState } from 'react';
import { formatNumber } from '@/utils/helper';

interface Props {
    open: boolean;
    onClose: () => void;
    title: string;
    btnTitle: string;
    form: InertiaFormProps<KhuyenMai>;
    onSubmit: () => void;
}

export function ModalDialog({ open, onClose, title, btnTitle, form, onSubmit }: Props) {
    const { data, setData, errors } = form;
    const [openDateStart, setOpenDateStart] = useState(false);
    const [openDateEnd, setOpenDateEnd] = useState(false);

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

                <form onSubmit={handleFormSubmit} className="mt-4 space-y-5">

                    <div className="grid gap-2">
                        <Label htmlFor="ma_khuyen_mai">Mã khuyến mãi</Label>
                        <Input
                            id="ma_khuyen_mai"
                            placeholder="Nhập mã"
                            value={data.ma_khuyen_mai ?? ''}
                            onChange={(e) => setData('ma_khuyen_mai', e.target.value)}
                        />
                        {errors.ma_khuyen_mai && <p className="text-red-500">{errors.ma_khuyen_mai}</p>}
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="ten_khuyen_mai">Tên khuyến mãi</Label>
                        <Input
                            id="ten_khuyen_mai"
                            placeholder="Nhập tên khuyến mãi"
                            value={data.ten_khuyen_mai}
                            onChange={(e) => setData('ten_khuyen_mai', e.target.value)}
                        />
                        {errors.ten_khuyen_mai && <p className="text-red-500">{errors.ten_khuyen_mai}</p>}
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="gia_tri">Giá trị</Label>
                        <Input
                            id="gia_tri"
                            type="text"
                            placeholder="Nhập giá trị"
                            value={formatNumber(data.gia_tri)}
                            onChange={(e) => {
                                const raw = e.target.value.replace(/\D/g, '');
                                setData('gia_tri', raw ? Number(raw) : 0);
                            }}
                        />
                        {errors.gia_tri && <p className="text-red-500">{errors.gia_tri}</p>}
                    </div>

                    <div className="grid gap-3">
                        <Label>Đơn vị tính</Label>
                        <Combobox
                            options={[
                                { value: 'percent', label: 'Phần trăm (%)' },
                                { value: 'fixed', label: 'Số tiền cố định (VNĐ)' },
                            ]}
                            value={data.don_vi_tinh}
                            onChange={(val) => setData('don_vi_tinh', val as string)}
                            placeholder="Chọn đơn vị"
                        />
                        {errors.don_vi_tinh && <p className="text-red-500">{errors.don_vi_tinh}</p>}
                    </div>

                    {/* Ngày bắt đầu */}
                    <div className="grid gap-3 grid-cols-2">
                        <div className="flex flex-col gap-3">
                            <Label htmlFor="ngay_bat_dau" className="px-1">
                                Ngày bắt đầu
                            </Label>

                            {/* Popover chọn ngày */}
                            <Popover open={openDateStart} onOpenChange={setOpenDateStart}>
                                <PopoverTrigger asChild>
                                    <Button
                                        variant="outline"
                                        id="ngay_bat_dau"
                                        className="w-full justify-between font-normal"
                                    >
                                        {data.ngay_bat_dau
                                            ? format(new Date(data.ngay_bat_dau), 'dd/MM/yyyy')
                                            : 'Chọn ngày'}
                                        <CalendarIcon className="ml-2 h-4 w-4 opacity-50" />
                                    </Button>
                                </PopoverTrigger>

                                <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                                    <Calendar
                                        className="w-[250px]"
                                        mode="single"
                                        selected={
                                            data.ngay_bat_dau ? new Date(data.ngay_bat_dau) : undefined
                                        }
                                        captionLayout="dropdown"
                                        onSelect={(selectedDate) => {
                                            if (!selectedDate) return;

                                            // Nếu đã có giờ -> giữ lại giờ cũ
                                            const oldTime = data.ngay_bat_dau
                                                ? new Date(data.ngay_bat_dau)
                                                : new Date();

                                            selectedDate.setHours(
                                                oldTime.getHours(),
                                                oldTime.getMinutes(),
                                                oldTime.getSeconds()
                                            );

                                            const formatted = format(selectedDate, 'yyyy-MM-dd HH:mm:ss');

                                            setData('ngay_bat_dau', formatted);
                                            setOpenDateStart(false);
                                        }}
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>

                        <div className="flex flex-col gap-3">
                            {/* Picker giờ */}
                            <Label htmlFor="ngay_bat_dau" className="px-1">
                                &nbsp;
                            </Label>
                            <Input
                                className="w-full"
                                type="time"
                                step="1" // hh:mm:ss
                                value={
                                    data.ngay_bat_dau
                                        ? format(new Date(data.ngay_bat_dau), 'HH:mm:ss')
                                        : '00:00:00'
                                }
                                onChange={(e) => {
                                    const [hour, minute, second] = e.target.value.split(':').map(Number);

                                    const baseDate = data.ngay_bat_dau
                                        ? new Date(data.ngay_bat_dau)
                                        : new Date();

                                    baseDate.setHours(hour, minute, second);

                                    const formatted = format(baseDate, 'yyyy-MM-dd HH:mm:ss');

                                    setData('ngay_bat_dau', formatted);
                                }}
                            />

                            {errors.ngay_bat_dau && (
                                <p className="text-red-500">{errors.ngay_bat_dau}</p>
                            )}
                        </div>
                    </div>

                    {/* Ngày kết thúc */}
                    <div className="grid gap-3 grid-cols-2">
                        <div className="flex flex-col gap-3">
                            <Label htmlFor="ngay_ket_thuc" className="px-1">
                                Ngày kết thúc
                            </Label>

                            {/* Popover chọn ngày */}
                            <Popover open={openDateEnd} onOpenChange={setOpenDateEnd}>
                                <PopoverTrigger asChild>
                                    <Button
                                        variant="outline"
                                        id="ngay_ket_thuc"
                                        className="w-full justify-between font-normal"
                                    >
                                        {data.ngay_ket_thuc
                                            ? format(new Date(data.ngay_ket_thuc), 'dd/MM/yyyy')
                                            : 'Chọn ngày'}
                                        <CalendarIcon className="ml-2 h-4 w-4 opacity-50" />
                                    </Button>
                                </PopoverTrigger>

                                <PopoverContent className="w-auto overflow-hidden p-0" align="start">
                                    <Calendar
                                        className="w-[250px]"
                                        mode="single"
                                        selected={
                                            data.ngay_ket_thuc ? new Date(data.ngay_ket_thuc) : undefined
                                        }
                                        captionLayout="dropdown"
                                        onSelect={(selectedDate) => {
                                            if (!selectedDate) return;

                                            // Nếu đã có giờ -> giữ lại giờ cũ
                                            const oldTime = data.ngay_ket_thuc
                                                ? new Date(data.ngay_ket_thuc)
                                                : new Date();

                                            selectedDate.setHours(
                                                oldTime.getHours(),
                                                oldTime.getMinutes(),
                                                oldTime.getSeconds()
                                            );

                                            const formatted = format(selectedDate, 'yyyy-MM-dd HH:mm:ss');

                                            setData('ngay_ket_thuc', formatted);
                                            setOpenDateEnd(false);
                                        }}
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>

                        <div className="flex flex-col gap-3">
                            {/* Picker giờ */}
                            <Label htmlFor="ngay_ket_thuc" className="px-1">
                                &nbsp;
                            </Label>
                            <Input
                                className="w-full"
                                type="time"
                                step="1" // hh:mm:ss
                                value={
                                    data.ngay_ket_thuc
                                        ? format(new Date(data.ngay_ket_thuc), 'HH:mm:ss')
                                        : '00:00:00'
                                }
                                onChange={(e) => {
                                    const [hour, minute, second] = e.target.value.split(':').map(Number);

                                    const baseDate = data.ngay_ket_thuc
                                        ? new Date(data.ngay_ket_thuc)
                                        : new Date();

                                    baseDate.setHours(hour, minute, second);

                                    const formatted = format(baseDate, 'yyyy-MM-dd HH:mm:ss');

                                    setData('ngay_ket_thuc', formatted);
                                }}
                            />

                            {errors.ngay_ket_thuc && (
                                <p className="text-red-500">{errors.ngay_ket_thuc}</p>
                            )}
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 pt-4">
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
