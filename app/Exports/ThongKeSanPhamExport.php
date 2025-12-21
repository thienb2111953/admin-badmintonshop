<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ThongKeSanPhamExport implements FromCollection, WithHeadings
{
  public function __construct(
    private $data,
    private string $title
  ) {}

  public function collection()
  {
    return $this->data;
  }

  // 👉 HEADING Ở ROW 2
  public function headings(): array
  {
    return [
      'Tên sản phẩm',
      'Số lượng tồn',
      'Số lượng bán',
      'Giá bán trung bình',
      'Doanh thu',
    ];
  }

  // 👉 CHÈN ROW 1 LÀM TITLE
  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        // Chèn 1 dòng trên cùng
        $event->sheet->insertNewRowBefore(1, 1);

        // Gộp ô A1:E1
        $event->sheet->mergeCells('A1:E1');

        // Set nội dung
        $event->sheet->setCellValue('A1', $this->title);

        // Style
        $event->sheet->getStyle('A1')->applyFromArray([
          'font' => [
            'bold' => true,
            'size' => 14,
          ],
        ]);
      },
    ];
  }
}
