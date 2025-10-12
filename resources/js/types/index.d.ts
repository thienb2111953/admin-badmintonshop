import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';
import { RouteDefinition } from '@/wayfinder';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: RouteDefinition<string> | string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href?: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
    items?: NavItem[];
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
    ten_thuong_hieu: string;
    logo_url: string | null;
    file_logo: File | null;
}

export interface DanhMuc {
    id_danh_muc: number;
    ten_danh_muc: string;
    slug: string;
    id_thuoc_tinh: string[];
    thuoc_tinhs: ThuocTinh[];
  }

export interface ThuocTinh{
    id_thuoc_tinh: number,
    ten_thuoc_tinh: string,
}

export interface ThuocTinhChiTiet{
    id_thuoc_tinh_chi_tiet: number,
    ten_thuoc_tinh_chi_tiet: string,
}

export interface DanhMucThuongHieu {
    id_danh_muc_thuong_hieu: number;
    ten_danh_muc_thuong_hieu: string;
    slug: string;
    mo_ta: string;
    id_thuong_hieu: number;
    id_danh_muc: number;
}

export interface Mau {
    id_mau: number;
    ten_mau: string;
  }

export interface KichThuoc {
    id_kich_thuoc: number;
    ten_kich_thuoc: string;
}

export interface Kho {
    id_san_pham_ton_kho: number;
    so_luong_nhap: number;
    ngay_nhap: date;
}

export interface SanPham {
    id_san_pham: number;
    ma_san_pham: string;
    ten_san_pham: string;
    mo_ta: string;
    gia_niem_yet: number | null;
    gia_ban: number | null;
    trang_thai: string;
}

export interface SanPhamChiTiet {
    id_san_pham_chi_tiet: number;
    slug: string;
    id_san_pham: number;
    id_mau: number;
    id_kich_thuoc: number;
    mau?: Mau;
    kich_thuoc?: KichThuoc;
    kho?: Kho;
  }

export interface AnhSanPham {
    id_anh_san_pham: number;
    id_san_pham_chi_tiet: number;
    ten_mau: string;
    files_anh_san_pham_new: File[];
    path_anh_san_pham_old: string[];
}

export interface CaiDat {
    id_cai_dat: number;
    ten_cai_dat: string;
    gia_tri: string;
}

export interface Banner {
  id_banner: number;
  img_url: string;
  thu_tu: number;
  href: string;
  file_logo: File | null;
}
