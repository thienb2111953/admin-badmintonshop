import { Button } from '@/components/ui/button';
import { ArrowLeft, Search } from 'lucide-react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import {
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
  type SortingState,
  type ColumnFiltersState,
  type VisibilityState,
} from '@tanstack/react-table';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { IconChevronLeft, IconChevronRight, IconChevronsLeft, IconChevronsRight } from '@tabler/icons-react';
import * as React from 'react';
import { Skeleton } from '@/components/ui/skeleton';
import { Link } from '@inertiajs/react';

function getValueByPath(obj: any, path: string) {
  return path.split('.').reduce((acc, key) => acc?.[key], obj);
}

interface TableCardProps {
  icon?: React.ElementType;
  data: any[];
  columns: any[];
  isLoading?: boolean;
  children?: React.ReactNode;
  filterConfig?: {
    columnId: string;
    label?: string;
  };
  backUrl?: string;
  backText?: string;
  title?: string;
  description?: string;
  subtitle?: string;
  subdescription?: string;
}

export default function TableCard({
  icon: Icon,
  data: initialData,
  columns,
  isLoading,
  children,
  filterConfig,
  ...props
}: TableCardProps) {
  const [data, setData] = React.useState(() => initialData);
  const [globalFilter, setGlobalFilter] = React.useState('');
  const [sorting, setSorting] = React.useState<SortingState>([]);
  const [columnFilters, setColumnFilters] = React.useState<ColumnFiltersState>([]);
  const [columnVisibility, setColumnVisibility] = React.useState<VisibilityState>({});

  React.useEffect(() => {
    setData(initialData);
  }, [initialData]);

  // GET options filter
  const options = React.useMemo(() => {
    const columnId = filterConfig?.columnId;
    if (!columnId) return [];

    const column = columns.find((col) => col.id === columnId);

    const accessor = column?.accessorFn
      ? (row: any) => column.accessorFn(row)
      : (row: any) => getValueByPath(row, columnId);

    const values = new Set(data.map((row) => accessor(row)).filter((val) => val !== undefined && val !== null));

    return Array.from(values);
  }, [data, filterConfig, columns]);

  const [pagination, setPagination] = React.useState({
    pageIndex: 0,
    pageSize: 10,
  });

  const table = useReactTable({
    data,
    columns,
    state: {
      sorting,
      columnVisibility,
      columnFilters,
      pagination,
      globalFilter: globalFilter,
    },
    onSortingChange: setSorting,
    onColumnFiltersChange: setColumnFilters,
    onColumnVisibilityChange: setColumnVisibility,
    onPaginationChange: setPagination,
    getCoreRowModel: getCoreRowModel(),
    onGlobalFilterChange: setGlobalFilter,
    globalFilterFn: (row, columnId, filterValue) => {
      return Object.values(row.original).some((value) =>
        String(value).toLowerCase().includes(String(filterValue).toLowerCase()),
      );
    },
    getFilteredRowModel: getFilteredRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
  });

  return (
    <>
      {props.backUrl && (
        <Link href={props.backUrl}>
          <Button className="mb-5" variant="outline" size="sm">
            <ArrowLeft className="mr-2 h-4 w-4" />
            {props.backText || ''}
          </Button>
        </Link>
      )}
      <div className="mb-5 flex items-center justify-between">
        <div className="flex items-center space-x-4">
          <div>
            <h1 className="mb-2 text-2xl font-bold text-gray-900">{props.title}</h1>
            <p className="text-gray-600">{props.description}</p>
          </div>
        </div>
        {children}
      </div>

      <Card className="mb-6 gap-2">
        <CardHeader>
          <div className="flex flex-wrap items-center justify-between space-y-5">
            <div>
              <CardTitle className="flex items-center text-lg">
                {Icon && <Icon className="mr-2 h-5 w-5" />}
                {props.subtitle}
              </CardTitle>
              <CardDescription>{props.subdescription}</CardDescription>
            </div>
            <div className="flex items-center space-x-2">
              {/*Input filtering*/}
              <Search className="h-4 w-4 text-gray-400" />
              <Input
                placeholder="Tìm kiếm thông tin"
                className="w-64"
                value={globalFilter}
                onChange={(e) => setGlobalFilter(e.target.value)}
              />

              {/*Select filtering*/}
              {filterConfig?.columnId && (
                <Select
                  value={table.getColumn(filterConfig.columnId)?.getFilterValue() ?? ''}
                  onValueChange={(value) =>
                    table.getColumn(filterConfig.columnId)?.setFilterValue(value === 'all' ? undefined : value)
                  }
                >
                  <SelectTrigger className="w-55">
                    <SelectValue placeholder={filterConfig.label || 'Chọn lọc'} />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="all">Tất cả</SelectItem>
                    {options.map((option) => (
                      <SelectItem key={option as string} value={option as string}>
                        {option as string}
                      </SelectItem>
                    ))}
                  </SelectContent>
                </Select>
              )}
            </div>
          </div>
        </CardHeader>
        <CardContent className="px-0">
          <div className="w-full flex-col justify-start gap-6">
            <div className="relative flex flex-col gap-4 overflow-auto px-4 lg:px-6">
              <div className="overflow-hidden rounded-lg border">
                <Table>
                  <TableHeader className="sticky top-0 z-10 bg-muted">
                    {table.getHeaderGroups().map((headerGroup) => (
                      <TableRow key={headerGroup.id}>
                        {headerGroup.headers.map((header) => {
                          return (
                            <TableHead key={header.id}>
                              {header.isPlaceholder
                                ? null
                                : flexRender(header.column.columnDef.header, header.getContext())}
                            </TableHead>
                          );
                        })}
                      </TableRow>
                    ))}
                  </TableHeader>
                  <TableBody>
                    {isLoading ? (
                      <>
                        <TableRow>
                          {columns.map((_, j) => (
                            <TableCell key={`head-${j}`}>
                              <Skeleton className="h-4 w-1/3 rounded" />
                            </TableCell>
                          ))}
                        </TableRow>
                        {[...Array(4)].map((_, i) => (
                          <TableRow key={i}>
                            {columns.map((_, j) => (
                              <TableCell key={`cell-${i}-${j}`}>
                                <Skeleton className={`h-4 rounded ${j % 2 === 0 ? 'w-3/4' : 'w-1/2'}`} />
                              </TableCell>
                            ))}
                          </TableRow>
                        ))}
                      </>
                    ) : table.getRowModel().rows?.length ? (
                      // Normal rows
                      table.getRowModel().rows.map((row) => (
                        <TableRow key={row.id}>
                          {row.getVisibleCells().map((cell) => (
                            <TableCell key={cell.id}>
                              {flexRender(cell.column.columnDef.cell, cell.getContext())}
                            </TableCell>
                          ))}
                        </TableRow>
                      ))
                    ) : (
                      // No result
                      <TableRow>
                        <TableCell colSpan={columns.length} className="h-24 text-center">
                          No results.
                        </TableCell>
                      </TableRow>
                    )}
                  </TableBody>
                </Table>
              </div>
              <div className="flex items-center justify-end px-4">
                <div className="flex w-full items-center gap-8 lg:w-fit">
                  <div className="hidden items-center gap-2 lg:flex">
                    <Label htmlFor="rows-per-page" className="text-sm font-medium">
                      Rows per page
                    </Label>
                    <Select
                      value={`${table.getState().pagination.pageSize}`}
                      onValueChange={(value) => {
                        table.setPageSize(Number(value));
                      }}
                    >
                      <SelectTrigger size="sm" className="w-20" id="rows-per-page">
                        <SelectValue placeholder={table.getState().pagination.pageSize} />
                      </SelectTrigger>
                      <SelectContent side="top">
                        {[10, 20, 30, 40, 50].map((pageSize) => (
                          <SelectItem key={pageSize} value={`${pageSize}`}>
                            {pageSize}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  </div>
                  <div className="flex w-fit items-center justify-center text-sm font-medium">
                    Page {table.getState().pagination.pageIndex + 1} of {table.getPageCount()}
                  </div>
                  <div className="ml-auto flex items-center gap-2 lg:ml-0">
                    <Button
                      variant="outline"
                      className="hidden h-8 w-8 p-0 lg:flex"
                      onClick={() => table.setPageIndex(0)}
                      disabled={!table.getCanPreviousPage()}
                    >
                      <span className="sr-only">Go to first page</span>
                      <IconChevronsLeft />
                    </Button>
                    <Button
                      variant="outline"
                      className="size-8"
                      size="icon"
                      onClick={() => table.previousPage()}
                      disabled={!table.getCanPreviousPage()}
                    >
                      <span className="sr-only">Go to previous page</span>
                      <IconChevronLeft />
                    </Button>
                    <Button
                      variant="outline"
                      className="size-8"
                      size="icon"
                      onClick={() => table.nextPage()}
                      disabled={!table.getCanNextPage()}
                    >
                      <span className="sr-only">Go to next page</span>
                      <IconChevronRight />
                    </Button>
                    <Button
                      variant="outline"
                      className="hidden size-8 lg:flex"
                      size="icon"
                      onClick={() => table.setPageIndex(table.getPageCount() - 1)}
                      disabled={!table.getCanNextPage()}
                    >
                      <span className="sr-only">Go to last page</span>
                      <IconChevronsRight />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </>
  );
}
