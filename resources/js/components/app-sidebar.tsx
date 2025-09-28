import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import {
  dashboard,
  quyen,
  thuong_hieu,
  nguoi_dung,
  danh_muc,
  thuoc_tinh,
  san_pham_thuong_hieu,
  cai_dat,
  mau,
  kich_thuoc,
} from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Folder, LayoutGrid, User, UserLock, Settings, ChevronDown } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
  {
    title: 'Dashboard',
    href: dashboard(),
    icon: LayoutGrid,
  },
  {
    title: 'Người dùng',
    href: nguoi_dung(),
    icon: User,
  },
  {
    title: 'Quyền',
    href: quyen(),
    icon: UserLock,
  },
  {
    title: 'Cài đặt',
    href: cai_dat(),
    icon: Settings,
  },
  {
    title: 'Quản lý thông tin',
    icon: Folder,
    items: [
      {
        title: 'Thương hiệu',
        href: thuong_hieu(),
      },
      {
        title: 'Danh mục',
        href: danh_muc(),
      },
      {
        title: 'Thuộc tính',
        href: thuoc_tinh(),
      },
      {
        title: 'Màu',
        href: mau(),
      },
      {
        title: 'Kích thước',
        href: kich_thuoc(),
      },
    ],
  },
  {
    title: 'Sản phẩm',
    href: san_pham_thuong_hieu(),
  },
];

const footerNavItems: NavItem[] = [
  {
    title: 'Repository',
    href: 'https://github.com/laravel/react-starter-kit',
    icon: Folder,
  },
  {
    title: 'Documentation',
    href: 'https://laravel.com/docs/starter-kits#react',
    icon: BookOpen,
  },
];

export function AppSidebar() {
  return (
    <Sidebar collapsible="icon" variant="inset">
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton size="lg" asChild>
              <Link href={dashboard()} prefetch>
                <AppLogo />
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <SidebarContent>
        <NavMain items={mainNavItems} />
      </SidebarContent>

      <SidebarFooter>
        <NavFooter items={footerNavItems} className="mt-auto" />
        <NavUser />
      </SidebarFooter>
    </Sidebar>
  );
}
