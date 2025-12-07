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
    SidebarMenuItem
} from '@/components/ui/sidebar';
import {
    dashboard,
    thuong_hieu,
    nguoi_dung,
    danh_muc,
    thuoc_tinh,
    san_pham_thuong_hieu,
    mau,
    kich_thuoc,
    banner,
    nhap_hang,
    thanh_toan,
    don_hang,
    thong_ke, khuyen_mai
} from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import {
    BookOpen,
    Folder,
    LayoutGrid,
    User,
    UserLock,
    Settings,
    Package,
    ChevronDown,
    PictureInPicture2,
    ChartNoAxesCombined
} from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Thống kê báo cáo',
        icon: ChartNoAxesCombined,
        items: [
            {
                title: 'Doanh thu',
                href: dashboard()
            },
            {
                title: 'Sản phẩm',
                href: thong_ke()
            }
        ]
    },
    {
        title: 'Người dùng',
        href: nguoi_dung(),
        icon: User
    },
    {
        title: 'Banner',
        href: banner(),
        icon: PictureInPicture2
    },
    {
        title: 'Quản lý thông tin',
        icon: Folder,
        items: [
            {
                title: 'Thương hiệu',
                href: thuong_hieu()
            },
            {
                title: 'Thuộc tính',
                href: thuoc_tinh()
            },
            {
                title: 'Danh mục',
                href: danh_muc()
            }
        ]
    },
    {
        title: 'Quản lý sản phẩm',
        icon: Package,
        items: [
            {
                title: 'Sản phẩm',
                href: san_pham_thuong_hieu()
            },
            {
                title: 'Màu',
                href: mau()
            },
            {
                title: 'Kích thước',
                href: kich_thuoc()
            }
        ]
    },
    {
        title: 'Nhập hàng',
        href: nhap_hang()
    },
    {
        title: 'Đơn hàng',
        href: don_hang()
    },
    {
        title: 'Thanh toán',
        href: thanh_toan()
    },
    {
        title: 'Khuyến mãi',
        href: khuyen_mai()
    }
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Repository',
    //     href: 'https://github.com/laravel/react-starter-kit',
    //     icon: Folder
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#react',
    //     icon: BookOpen
    // }
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
