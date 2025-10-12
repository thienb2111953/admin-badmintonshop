import AppLayout from '@/layouts/app-layout';
import { Head, useForm, router } from '@inertiajs/react';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Combobox } from '@/components/ui/combobox';
import { toast } from 'sonner';
import { slugify } from 'transliteration';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem, DanhMuc, DanhMucThuongHieu, ThuongHieu } from '@/types';
import { san_pham_thuong_hieu } from '@/routes';
import { Editor } from '@/components/blocks/editor-x/editor';
import { SerializedEditorState } from 'lexical';

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
            version: 1,
          },
        ],
        direction: 'ltr',
        format: '',
        indent: 0,
        type: 'paragraph',
        version: 1,
      },
    ],
    direction: 'ltr',
    format: '',
    indent: 0,
    type: 'root',
    version: 1,
  },
} as unknown as SerializedEditorState;

export default function CreatePage({
  thuong_hieus,
  danh_mucs,
  danh_muc_thuong_hieu,
}: {
  thuong_hieus: ThuongHieu[];
  danh_mucs: DanhMuc[];
  danh_muc_thuong_hieu?: DanhMucThuongHieu;
}) {
  const [editorState, setEditorState] = useState<SerializedEditorState>(initialValue);
  const [htmlContent, setHtmlContent] = useState('<p>Initial content</p>');
  const form = useForm({
    id_danh_muc_thuong_hieu: danh_muc_thuong_hieu?.id_danh_muc_thuong_hieu ?? 0,
    ten_danh_muc_thuong_hieu: danh_muc_thuong_hieu?.ten_danh_muc_thuong_hieu ?? '',
    slug: danh_muc_thuong_hieu?.slug ?? '',
    mo_ta: danh_muc_thuong_hieu?.mo_ta ?? '',
    id_danh_muc: danh_muc_thuong_hieu?.id_danh_muc ?? 0,
    id_thuong_hieu: danh_muc_thuong_hieu?.id_thuong_hieu ?? 0,
  });

  const { data, setData, errors, processing } = form;

  const breadcrumbs: BreadcrumbItem[] = [
    { title: `Quản lý Danh mục Thương hiệu`, href: san_pham_thuong_hieu() },
    {
      title: data.id_danh_muc_thuong_hieu ? 'Cập nhật' : 'Thêm',
      href: '#',
    },
  ];

  function updateTenDanhMucThuongHieu(id_danh_muc: number, id_thuong_hieu: number) {
    const danhMuc = danh_mucs.find((dm) => dm.id_danh_muc === id_danh_muc);
    const thuongHieu = thuong_hieus.find((th) => th.id_thuong_hieu === id_thuong_hieu);

    if (danhMuc && thuongHieu) {
      const ten = `${danhMuc.ten_danh_muc} ${thuongHieu.ten_thuong_hieu}`;
      setData('ten_danh_muc_thuong_hieu', ten);
      setData('slug', slugify(ten));
    }
  }

  useEffect(() => {
    if (data.id_danh_muc && data.id_thuong_hieu) {
      updateTenDanhMucThuongHieu(data.id_danh_muc, data.id_thuong_hieu);
    }
  }, [data.id_danh_muc, data.id_thuong_hieu]);

  useEffect(() => {
    if (danh_muc_thuong_hieu?.mo_ta) {
      try {
        const parsed = JSON.parse(danh_muc_thuong_hieu.mo_ta);
        setEditorState(parsed);
      } catch (e) {
        console.warn('mo_ta không phải JSON hợp lệ, fallback về HTML:', e);
      }
    }
  }, [danh_muc_thuong_hieu]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (data.id_danh_muc_thuong_hieu && data.id_danh_muc_thuong_hieu !== 0) {
      // Update
      form.put(route('danh_muc_thuong_hieu.update', { id: data.id_danh_muc_thuong_hieu }), {
        onSuccess: () => {
          toast.success('Cập nhật thành công!');
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    } else {
      // Create
      form.post(route('danh_muc_thuong_hieu.store'), {
        onSuccess: () => {
          toast.success('Tạo mới thành công!');
        },
        onError: (errors) => Object.values(errors).forEach((err) => toast.error(err as string)),
      });
    }
  };

  const [editorState, setEditorState] = useState<SerializedEditorState>(initialValue);
  const [htmlContent, setHtmlContent] = useState('');

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={data.id_danh_muc_thuong_hieu ? 'Cập nhật Danh mục Thương hiệu' : 'Thêm Danh mục Thương hiệu'} />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <h1 className="mb-6 text-xl font-bold">
          {data.id_danh_muc_thuong_hieu ? 'Cập nhật Danh mục Thương hiệu' : 'Thêm Danh mục Thương hiệu'}
        </h1>
        <form onSubmit={handleSubmit} className="space-y-5">
          <div className="grid grid-cols-2 gap-6">
            <div className="grid gap-3">
              <Label>Thương hiệu</Label>
              <Combobox
                options={thuong_hieus.map((th) => ({
                  label: th.ten_thuong_hieu,
                  value: th.id_thuong_hieu,
                }))}
                value={data.id_thuong_hieu}
                onChange={(val) => setData('id_thuong_hieu', val as number)}
                placeholder="Chọn thương hiệu..."
                className="w-full"
              />
              {errors.id_thuong_hieu && <p className="text-red-500">{errors.id_thuong_hieu}</p>}
            </div>

            <div className="grid gap-3">
              <Label>Danh mục</Label>
              <Combobox
                options={danh_mucs.map((dm) => ({
                  label: dm.ten_danh_muc,
                  value: dm.id_danh_muc,
                }))}
                value={data.id_danh_muc}
                onChange={(val) => setData('id_danh_muc', val as number)}
                placeholder="Chọn danh mục..."
                className="w-full"
              />
              {errors.id_danh_muc && <p className="text-red-500">{errors.id_danh_muc}</p>}
            </div>

            {/* Input tên danh mục thương hiệu */}
            <div className="grid gap-3">
              <Label htmlFor="ten_danh_muc_thuong_hieu">Tên danh mục thương hiệu</Label>
              <Input
                id="ten_danh_muc_thuong_hieu"
                placeholder="Tên danh mục thương hiệu"
                value={data.ten_danh_muc_thuong_hieu ?? ''}
                onChange={(e) => {
                  const value = e.target.value;
                  setData('ten_danh_muc_thuong_hieu', value);
                  setData('slug', slugify(value));
                }}
              />
              {errors.ten_danh_muc_thuong_hieu && <p className="text-red-500">{errors.ten_danh_muc_thuong_hieu}</p>}
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
          </div>
          <div className="grid gap-3">
            <Label htmlFor="mo_ta">Mô tả</Label>
            <Editor
              initialHtml={danh_muc_thuong_hieu?.mo_ta || ''}
              //   editorSerializedState={editorState}
              onSerializedChange={(value) => setEditorState(value)}
              // onHtmlChange={(html) => {
              //   setHtmlContent(html);
              // }}
              onChange={(value)=>console.log(value)}
            />
            {errors.mo_ta && <p className="text-red-500">{errors.mo_ta}</p>}
          </div>
          <div className="flex justify-end gap-3 pt-4">
            <Button type="button" variant="outline" onClick={() => router.visit(route('danh_muc_thuong_hieu.index'))}>
              Hủy
            </Button>
            <Button type="submit" disabled={processing}>
              {data.id_danh_muc_thuong_hieu ? 'Cập nhật' : 'Thêm'}
            </Button>
          </div>
        </form>
      </div>
    </AppLayout>
  );
}
