import * as React from 'react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Input } from '@/components/ui/input';
import {
  ColumnDef,
  ColumnFiltersState,
  SortingState,
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
  VisibilityState,
} from '@tanstack/react-table';
import { Plus } from 'lucide-react';

import { DataTablePagination } from '@/components/custom/data-table-pagination';

interface DataTableProps<TData, TValue> {
  columns: ColumnDef<TData, TValue>[];
  data: TData[];
  onAdd: () => void;
  onDeleteSelected?: (selectedRows: TData[]) => void; // truyền cả row chứ không chỉ id
  tableInstanceRef?: (table: ReturnType<typeof useReactTable<TData>>) => void;
}

export function DataTable<TData, TValue>({
  columns,
  data,
  onAdd,
  onDeleteSelected,
  tableInstanceRef,
}: DataTableProps<TData, TValue>) {
  const [sorting, setSorting] = React.useState<SortingState>([]);
  const [columnVisibility, setColumnVisibility] = React.useState<VisibilityState>({});

  const [rowSelection, setRowSelection] = React.useState({});
  const [columnFilters, setColumnFilters] = React.useState<ColumnFiltersState>([]);
  const [globalFilter, setGlobalFilter] = React.useState('');

  const table = useReactTable({
    data,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    onSortingChange: setSorting,
    getSortedRowModel: getSortedRowModel(),
    onColumnFiltersChange: setColumnFilters,
    getFilteredRowModel: getFilteredRowModel(),
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    onGlobalFilterChange: setGlobalFilter,
    state: {
      sorting,
      columnFilters,
      columnVisibility,
      rowSelection,
      globalFilter,
    },
  });

  React.useEffect(() => {
    if (tableInstanceRef) tableInstanceRef(table);
  }, [table]);

  // const handleDeleteSelected = () => {
  //   const selectedIds = Object.keys(rowSelection)
  //     .filter((id) => rowSelection[id])
  //     .map((id) => table.getRow(id).original.id_quyen);

  //   if (onDeleteSelected) onDeleteSelected(selectedIds);
  // };

  return (
    <div>
      <div className="flex items-center justify-between py-4">
        <div className="item-left">
          <Button onClick={onAdd}>
            <Plus className="h-4 w-4" />
            <Label>Thêm</Label>
          </Button>
        </div>
        <div className="item-right">
          {/* <DataTableViewOptions table={table} /> */}

          <Input
            placeholder="Tìm kiếm ..."
            value={globalFilter ?? ''}
            onChange={(event) => setGlobalFilter(event.target.value)}
            className="float-right w-100"
          />
        </div>
      </div>

      <div className="overflow-hidden rounded-md border">
        <Table>
          <TableHeader>
            {table.getHeaderGroups().map((headerGroup) => (
              <TableRow key={headerGroup.id}>
                {headerGroup.headers.map((header) => {
                  return (
                    <TableHead key={header.id}>
                      {header.isPlaceholder ? null : flexRender(header.column.columnDef.header, header.getContext())}
                    </TableHead>
                  );
                })}
              </TableRow>
            ))}
          </TableHeader>
          <TableBody>
            {table.getRowModel().rows?.length ? (
              table.getRowModel().rows.map((row) => (
                <TableRow key={row.id} data-state={row.getIsSelected() && 'selected'}>
                  {row.getVisibleCells().map((cell) => (
                    <TableCell key={cell.id}>{flexRender(cell.column.columnDef.cell, cell.getContext())}</TableCell>
                  ))}
                </TableRow>
              ))
            ) : (
              <TableRow>
                <TableCell colSpan={columns.length} className="h-24 text-center">
                  No results.
                </TableCell>
              </TableRow>
            )}
          </TableBody>
        </Table>
      </div>

      <div className="space-x-2 py-4">
        <DataTablePagination
          table={table}
          // onDeleteSelected={() => {
          //   const selectedRows = table.getSelectedRowModel().flatRows.map((row) => row.original);
          //   if (onDeleteSelected) onDeleteSelected(selectedRows);
          // }}
        />
      </div>
      
    </div>
  );
}
