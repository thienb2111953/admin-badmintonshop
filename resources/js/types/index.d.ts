import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id_nguoi_dung: number;
    name: string;
    email: string;
    ngay_sinh: date;
    sdt: string;
    password: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Quyen {
    id_quyen: number;
    ten_quyen: string;
}

export interface ThuongHieu{
    id_thuong_hieu: number;
    ma_thuong_hieu: string;
    ten_thuong_hieu: string;
    logo_url: string | null;
    file_logo: File | null;
}

export interface DanhMuc {
    id_danh_muc: number;
    ten_danh_muc: string;
    slug: string;
    thuoc_tinhs?: ThuocTinh[];
  }
  
  export interface DanhMucForm {
    id_danh_muc: number;
    ten_danh_muc: string;
    slug: string;
    id_thuoc_tinh: string[]; // mảng id để submit
  }

export interface ThuocTinh{
    id_thuoc_tinh: number,
    ten_thuoc_tinh: string,
}

export interface ThuocTinhChiTiet{
    id_thuoc_tinh_chi_tiet: number,
    ten_thuoc_tinh_chi_tiet: string,
}
