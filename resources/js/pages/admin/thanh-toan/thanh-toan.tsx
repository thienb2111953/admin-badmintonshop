import AppLayout from '@/layouts/app-layout';
import { columns } from './columns';
import { DataTable } from '@/components/custom/data-table';
import { type BreadcrumbItem, ThanhToan } from '@/types';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import { ModalDialog } from './modal-dialog';
import { DialogConfirmDelete } from '@/components/custom/dialog-confirm-delete';
import { toast } from 'sonner';
import { thanh_toan } from '@/routes';

export default function ThanhToanPage({ thanh_toans }: { thanh_toans: ThanhToan[] }) {

  const breadcrumbs: BreadcrumbItem[] = [{ title: 'Quản lý Thanh toán', href: thanh_toan() }];

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Quản lý Thanh toán" />

      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        <DataTable columns={columns()} data={thanh_toans} showAddButton={false}/>
      </div>

    </AppLayout>
  );
}
