import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import { type BreadcrumbItem } from '@/types';
import { type PropsWithChildren, useState } from 'react';
import { Toaster } from '@/components/ui/sonner';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';

export default function AppHeaderLayout({
  children,
  breadcrumbs,
}: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
  const [queryClient] = useState(() => new QueryClient());

  return (
    <QueryClientProvider client={queryClient}>
      <AppShell>
        <AppHeader breadcrumbs={breadcrumbs} />
        <AppContent>{children}</AppContent>
        <Toaster position={'top-right'} />
      </AppShell>
    </QueryClientProvider>
  );
}
