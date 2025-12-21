import AppLayout from '@/layouts/app-layout';
import { columns, type ThongKeSanPham } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { thong_ke } from '@/routes';

import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Input } from '@/components/ui/input';

/* ===================== TYPES ===================== */
interface Filters {
  type: 'month' | 'quarter' | 'year';
}

interface Props {
  thong_ke_san_phams: ThongKeSanPham[];
  filters: Filters;
}

type ExportType = 'month' | 'quarter' | 'year';

/* ===================== COMPONENT ===================== */
export default function ThongKeSanPhamPage({ thong_ke_san_phams, filters }: Props) {
  /* ---------- FILTER ---------- */
  const [timeRange, setTimeRange] = useState<ExportType>(filters.type || 'month');

  /* ---------- EXPORT ---------- */
  const [openExport, setOpenExport] = useState(false);
  const [exportType, setExportType] = useState<ExportType>('month');
  const [month, setMonth] = useState('');
  const [year, setYear] = useState(new Date().getFullYear().toString());
  const [quarter, setQuarter] = useState('1');

  const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Thống kê sản phẩm', href: thong_ke() },
  ];

  /* ===================== HANDLERS ===================== */
  const handleFilterChange = (newType: ExportType) => {
    setTimeRange(newType);

    router.get(
      thong_ke(),
      { type: newType },
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      },
    );
  };

  const handleExport = () => {
    const params: Record<string, string> = { type: exportType };

    if (exportType === 'month') {
      if (!month) return;
      params.month = month;
    }

    if (exportType === 'quarter') {
      if (!year || !quarter) return;
      params.year = year;
      params.quarter = quarter;
    }

    if (exportType === 'year') {
      if (!year) return;
      params.year = year;
    }

    setOpenExport(false);
    window.location.href = route('thong_ke.export', params);
  };

  /* ===================== RENDER ===================== */
  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Thống kê sản phẩm" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        {/* ===== FILTER + EXPORT ===== */}
        <div className="flex items-center justify-between py-2">
          <div className="flex gap-2">
            <Button
              onClick={() => handleFilterChange('month')}
              variant={timeRange === 'month' ? 'default' : 'outline'}
            >
              Theo Tháng
            </Button>
            <Button
              onClick={() => handleFilterChange('quarter')}
              variant={timeRange === 'quarter' ? 'default' : 'outline'}
            >
              Theo Quý
            </Button>
            <Button
              onClick={() => handleFilterChange('year')}
              variant={timeRange === 'year' ? 'default' : 'outline'}
            >
              Theo Năm
            </Button>
          </div>

          <Button variant="secondary" onClick={() => setOpenExport(true)}>
            Xuất Excel
          </Button>
        </div>

        {/* ===== TABLE ===== */}
        <DataTable
          columns={columns}
          data={thong_ke_san_phams}
          showAddButton={false}
        />
      </div>

      {/* ===================== EXPORT MODAL ===================== */}
      <Dialog open={openExport} onOpenChange={setOpenExport}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Xuất danh sách sản phẩm đã bán</DialogTitle>
          </DialogHeader>

          <div className="space-y-4">
            <Select
              value={exportType}
              onValueChange={(v) => {
                setExportType(v as ExportType);
                setMonth('');
                setQuarter('1');
              }}
            >
              <SelectTrigger>
                <SelectValue placeholder="Chọn kiểu thống kê" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="month">Theo tháng</SelectItem>
                <SelectItem value="quarter">Theo quý</SelectItem>
                <SelectItem value="year">Theo năm</SelectItem>
              </SelectContent>
            </Select>

            {exportType === 'month' && (
              <Input
                type="month"
                value={month}
                onChange={(e) => setMonth(e.target.value)}
              />
            )}

            {exportType === 'quarter' && (
              <div className="flex gap-2">
                <Input
                  type="number"
                  min={2000}
                  max={new Date().getFullYear()}
                  value={year}
                  onChange={(e) => setYear(e.target.value)}
                  placeholder="Năm"
                />
                <Select value={quarter} onValueChange={setQuarter}>
                  <SelectTrigger>
                    <SelectValue />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="1">Quý 1</SelectItem>
                    <SelectItem value="2">Quý 2</SelectItem>
                    <SelectItem value="3">Quý 3</SelectItem>
                    <SelectItem value="4">Quý 4</SelectItem>
                  </SelectContent>
                </Select>
              </div>
            )}

            {exportType === 'year' && (
              <Input
                type="number"
                min={2000}
                max={new Date().getFullYear()}
                value={year}
                onChange={(e) => setYear(e.target.value)}
                placeholder="Năm"
              />
            )}

            <Button
              className="w-full"
              onClick={handleExport}
              disabled={
                (exportType === 'month' && !month) ||
                (exportType === 'quarter' && (!year || !quarter)) ||
                (exportType === 'year' && !year)
              }
            >
              Xuất file Excel
            </Button>
          </div>
        </DialogContent>
      </Dialog>
    </AppLayout>
  );
}
