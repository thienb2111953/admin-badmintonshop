export function formatNumber(value: number | null | undefined): string {
  if (value == null || isNaN(value)) return '';
  return value.toLocaleString('vi-VN'); // ví dụ: 1000000 -> "1.000.000"
}
