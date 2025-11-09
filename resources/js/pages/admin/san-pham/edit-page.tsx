import AppLayout from '@/layouts/app-layout';
import { Head, useForm, router } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { toast } from 'sonner';
import { slugify } from 'transliteration';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem, DanhMuc, SanPham, ThuongHieu } from '@/types';
import { san_pham as SanPhamRoute, san_pham_thuong_hieu } from '@/routes';
import { Editor } from '@/components/blocks/editor-x/editor';
import { SerializedEditorState } from 'lexical';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';

export const initialValue = {
    root: {
        children: [
            {
                children: [
                    {
                        detail: 0,
                        format: 0,
                        mode: 'normal',
                        style: '',
                        text: '',
                        type: 'text',
                        version: 1
                    }
                ],
                direction: 'ltr',
                format: '',
                indent: 0,
                type: 'paragraph',
                version: 1
            }
        ],
        direction: 'ltr',
        format: '',
        indent: 0,
        type: 'root',
        version: 1
    }
} as unknown as SerializedEditorState;

export default function EditPage({
                                     san_pham,
                                     id_danh_muc_thuong_hieu,
                                     ds_thuoc_tinh
                                 }: {
    san_pham?: SanPham;
    id_danh_muc_thuong_hieu?: number;
    ds_thuoc_tinh: any;
}) {
    const [editorState, setEditorState] = useState<SerializedEditorState>(initialValue);
    const [htmlContent, setHtmlContent] = useState('<p>Initial content</p>');
    const initialThuocTinhData = ds_thuoc_tinh.reduce((acc: any, tt: any) => {
        // Tìm thuộc tính chi tiết đã chọn cho sản phẩm hiện tại
        const selected = san_pham?.thuoc_tinhs?.find(
            (t: any) => t.thuoc_tinh.id_thuoc_tinh === tt.id_thuoc_tinh
        );
        // Khởi tạo key: thuoc_tinh_<id> với giá trị đã chọn hoặc rỗng
        acc[`thuoc_tinh_${tt.id_thuoc_tinh}`] = selected?.id_thuoc_tinh_chi_tiet || '';
        return acc;
    }, {});
    const form = useForm({
        id_san_pham: san_pham?.id_san_pham ?? 0,
        ten_san_pham: san_pham?.ten_san_pham ?? '',
        ma_san_pham: san_pham?.ma_san_pham ?? '',
        slug: san_pham?.slug ?? '',
        mo_ta: san_pham?.mo_ta ?? '',
        gia_niem_yet: san_pham?.gia_niem_yet ?? 0,
        gia_ban: san_pham?.gia_niem_yet ?? 0,
        trang_thai: san_pham?.trang_thai ?? '',
        ...initialThuocTinhData
    });

    const { data, setData, errors, processing } = form;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: `Quản lý Sản phẩm`, href: SanPhamRoute(id_danh_muc_thuong_hieu)
        },
        {
            title: data.id_san_pham ? 'Cập nhật' : 'Thêm',
            href: '#'
        }
    ];

    useEffect(() => {
        if (san_pham?.mo_ta) {
            try {
                const parsed = JSON.parse(san_pham.mo_ta);
                setEditorState(parsed);
            } catch (e) {
                console.warn('mo_ta không phải JSON hợp lệ, fallback về HTML:', e);
            }
        }
    }, [san_pham]);

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (data.id_san_pham && data.id_san_pham !== 0) {
            // Update
            form.put(route('san_pham.update', { id_danh_muc_thuong_hieu: id_danh_muc_thuong_hieu }), {
                onSuccess: () => {
                    toast.success('Cập nhật thành công!');
                },
                onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string))
            });
        } else {
            // Create
            form.post(route('san_pham.store', { id_danh_muc_thuong_hieu: id_danh_muc_thuong_hieu }), {
                onSuccess: () => {
                    toast.success('Tạo mới thành công!');
                },
                onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string))
            });
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={data.id_san_pham ? 'Cập nhật Sản phẩm' : 'Thêm Sản phẩm'} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <h1 className="mb-6 text-xl font-bold">{data.id_san_pham ? 'Cập nhật Sản phẩm' : 'Thêm Sản phẩm'}</h1>
                <form onSubmit={handleSubmit} className="space-y-5">
                    <div className="grid grid-cols-2 gap-6">
                        <div className="grid gap-3">
                            <Label htmlFor="ten_san_pham">Tên Sản phẩm</Label>
                            <Input
                                id="ten_san_pham"
                                placeholder="Tên Sản phẩm"
                                value={data.ten_san_pham ?? ''}
                                onChange={(e) => {
                                    const value = e.target.value;
                                    setData('ten_san_pham', value);
                                    setData('slug', slugify(value));
                                }}
                            />
                            {errors.ten_san_pham && <p className="text-red-500">{errors.ten_san_pham}</p>}
                        </div>

                        <div className="grid gap-3">
                            <Label htmlFor="slug">Slug</Label>
                            <Input
                                id="slug"
                                placeholder="Slug"
                                value={data.slug ?? ''}
                                onChange={(e) => setData('slug', e.target.value)}
                            />
                            {errors.slug && <p className="text-red-500">{errors.slug}</p>}
                        </div>

                        <div className="grid gap-3">
                            <Label htmlFor="ma_san_pham">Mã Sản phẩm</Label>
                            <Input
                                id="ma_san_pham"
                                placeholder="Mã Sản phẩm"
                                value={data.ma_san_pham ?? ''}
                                onChange={(e) => {
                                    const value = e.target.value;
                                    setData('ma_san_pham', value);
                                }}
                            />
                            {errors.ma_san_pham && <p className="text-red-500">{errors.ma_san_pham}</p>}
                        </div>

                        <div className="grid gap-3">
                            <Label htmlFor="trang_thai">Trạng thái</Label>
                            <RadioGroup value={data.trang_thai || ''}
                                        onValueChange={(val) => setData('trang_thai', val)}>
                                <div className="flex items-center space-x-2">
                                    <RadioGroupItem value="Đang sản xuất" id="dang-san-xuat" />
                                    <Label htmlFor="dang-san-xuat">Đang sản xuất</Label>
                                </div>
                                <div className="flex items-center space-x-2">
                                    <RadioGroupItem value="Hết sản xuất" id="het-san-xuat" />
                                    <Label htmlFor="het-san-xuat">Hết sản xuất</Label>
                                </div>
                            </RadioGroup>
                            {errors.trang_thai && <p className="text-red-500">{errors.trang_thai}</p>}
                        </div>
                    </div>
                    {/* Danh sách thuộc tính động */}
                    <div className="grid grid-cols-2 gap-6 mt-4">
                        {ds_thuoc_tinh.map((thuocTinh: any) => {
                            const fieldName = `thuoc_tinh_${thuocTinh.id_thuoc_tinh}`; // Tạo tên trường

                            return (
                                <div key={thuocTinh.id_thuoc_tinh} className="grid gap-3">
                                    <Label>{thuocTinh.ten_thuoc_tinh}</Label>
                                    <Combobox
                                        options={thuocTinh.chi_tiets.map((ct: any) => ({
                                            label: ct.ten_thuoc_tinh_chi_tiet,
                                            value: ct.id_thuoc_tinh_chi_tiet
                                        }))}
                                        // **THAY ĐỔI ĐỂ SỬ DỤNG GIÁ TRỊ TỪ data CỦA useForm**
                                        value={data[fieldName] || ''}
                                        // **SỬ DỤNG TÊN TRƯỜNG ĐỘNG ĐÃ KHAI BÁO**
                                        onChange={(val) => setData(fieldName, val)}
                                        placeholder={`Chọn ${thuocTinh.ten_thuoc_tinh}...`}
                                        className="w-full"
                                    />
                                    {errors[fieldName] && <p className="text-red-500">{errors[fieldName]}</p>}
                                </div>
                            );
                        })}
                    </div>


                    <div className="grid gap-3">
                        <Label htmlFor="mo_ta">Mô tả</Label>
                        <Editor
                            initialHtml={san_pham?.mo_ta || ''}
                            //   editorSerializedState={editorState}
                            //   onSerializedChange={(value) => setEditorState(value)}
                            onHtmlChange={(html) => {
                                setHtmlContent(html);
                                setData('mo_ta', html);
                            }}
                            //   onChange={(value) => console.log(value)}
                        />
                        {errors.mo_ta && <p className="text-red-500">{errors.mo_ta}</p>}
                    </div>
                    <div className="flex justify-end gap-3 pt-4">
                        <Button type="button" variant="outline"
                                onClick={() => router.visit(SanPhamRoute(id_danh_muc_thuong_hieu))}>
                            Hủy
                        </Button>
                        <Button type="submit" disabled={processing}>
                            {data.id_san_pham ? 'Cập nhật' : 'Thêm'}
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
