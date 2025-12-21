<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
class ThongKeSanPhamExport implements FromCollection, WithHeadings, WithEvents, WithMapping
{
  public function __construct(
    private $data,
    private string $title
  ) {}

  public function collection()
  {
    return $this->data;
  }

  public function headings(): array
  {
    return [
      'Tên sản phẩm',
      'Số lượng tồn',
      'Số lượng bán',
      'Giá nhập TB',
      'Giá bán TB',
      'Doanh thu',
      'Lợi nhuận',
    ];
  }

  public function map($row): array
  {
    return [
      $row->ten_san_pham_chi_tiet,
      $row->so_luong_ton,
      $row->so_luong_ban,
      (float) $row->gia_nhap_tb,
      (float) $row->gia_ban_tb,
      (float) $row->doanh_thu,
      (float) $row->loi_nhuan,
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $event->sheet->insertNewRowBefore(1, 1);
        $event->sheet->mergeCells('A1:G1');
        $event->sheet->setCellValue('A1', $this->title);
        $event->sheet->getStyle('A1')->applyFromArray([
          'font' => ['bold' => true, 'size' => 14],
        ]);
      },
    ];
  }
}
