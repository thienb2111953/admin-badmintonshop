--
-- PostgreSQL database dump
--

\restrict Q0D6xhCAOK3fhcRZPQ5hwjlcbLjdEakkr0nfUe82SQHXOkUkoIAFv3GTjMF0ZGe

-- Dumped from database version 16.10 (Ubuntu 16.10-1.pgdg24.04+1)
-- Dumped by pg_dump version 16.10 (Ubuntu 16.10-1.pgdg24.04+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: anh_san_pham; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.anh_san_pham (
    id_anh_san_pham bigint NOT NULL,
    anh_url character varying(255) NOT NULL,
    thu_tu integer NOT NULL,
    id_san_pham_chi_tiet bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.anh_san_pham OWNER TO postgres;

--
-- Name: anh_san_pham_id_anh_san_pham_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.anh_san_pham_id_anh_san_pham_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.anh_san_pham_id_anh_san_pham_seq OWNER TO postgres;

--
-- Name: anh_san_pham_id_anh_san_pham_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.anh_san_pham_id_anh_san_pham_seq OWNED BY public.anh_san_pham.id_anh_san_pham;


--
-- Name: banner; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.banner (
    id_banner bigint NOT NULL,
    img_url character varying(255) NOT NULL,
    thu_tu integer NOT NULL,
    href character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.banner OWNER TO postgres;

--
-- Name: banner_id_banner_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.banner_id_banner_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.banner_id_banner_seq OWNER TO postgres;

--
-- Name: banner_id_banner_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.banner_id_banner_seq OWNED BY public.banner.id_banner;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO postgres;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO postgres;

--
-- Name: cai_dat; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.cai_dat (
    id_cai_dat bigint NOT NULL,
    ten_cai_dat character varying(255) NOT NULL,
    gia_tri character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cai_dat OWNER TO postgres;

--
-- Name: cai_dat_id_cai_dat_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.cai_dat_id_cai_dat_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cai_dat_id_cai_dat_seq OWNER TO postgres;

--
-- Name: cai_dat_id_cai_dat_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.cai_dat_id_cai_dat_seq OWNED BY public.cai_dat.id_cai_dat;


--
-- Name: danh_muc; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.danh_muc (
    id_danh_muc bigint NOT NULL,
    ten_danh_muc character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.danh_muc OWNER TO postgres;

--
-- Name: danh_muc_id_danh_muc_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.danh_muc_id_danh_muc_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.danh_muc_id_danh_muc_seq OWNER TO postgres;

--
-- Name: danh_muc_id_danh_muc_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.danh_muc_id_danh_muc_seq OWNED BY public.danh_muc.id_danh_muc;


--
-- Name: danh_muc_thuoc_tinh; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.danh_muc_thuoc_tinh (
    id_danh_muc_thuoc_tinh bigint NOT NULL,
    id_thuoc_tinh bigint NOT NULL,
    id_danh_muc bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.danh_muc_thuoc_tinh OWNER TO postgres;

--
-- Name: danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq OWNER TO postgres;

--
-- Name: danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq OWNED BY public.danh_muc_thuoc_tinh.id_danh_muc_thuoc_tinh;


--
-- Name: danh_muc_thuong_hieu; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.danh_muc_thuong_hieu (
    id_danh_muc_thuong_hieu bigint NOT NULL,
    ten_danh_muc_thuong_hieu character varying(255) NOT NULL,
    slug character varying(255),
    mo_ta text,
    id_thuong_hieu bigint,
    id_danh_muc bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.danh_muc_thuong_hieu OWNER TO postgres;

--
-- Name: danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq OWNER TO postgres;

--
-- Name: danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq OWNED BY public.danh_muc_thuong_hieu.id_danh_muc_thuong_hieu;


--
-- Name: don_hang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.don_hang (
    id_don_hang bigint NOT NULL,
    ma_don_hang character varying(255) NOT NULL,
    id_nguoi_dung bigint,
    trang_thai_thanh_toan character varying(50) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    ngay_dat_hang timestamp without time zone DEFAULT now(),
    phuong_thuc_thanh_toan character varying(100),
    trang_thai_don_hang character varying(255)
);


ALTER TABLE public.don_hang OWNER TO postgres;

--
-- Name: don_hang_chi_tiet; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.don_hang_chi_tiet (
    id_don_hang_chi_tiet bigint NOT NULL,
    id_don_hang bigint NOT NULL,
    id_san_pham_chi_tiet bigint NOT NULL,
    so_luong integer NOT NULL,
    don_gia numeric(15,0) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.don_hang_chi_tiet OWNER TO postgres;

--
-- Name: don_hang_chi_tiet_id_don_hang_chi_tiet_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.don_hang_chi_tiet_id_don_hang_chi_tiet_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.don_hang_chi_tiet_id_don_hang_chi_tiet_seq OWNER TO postgres;

--
-- Name: don_hang_chi_tiet_id_don_hang_chi_tiet_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.don_hang_chi_tiet_id_don_hang_chi_tiet_seq OWNED BY public.don_hang_chi_tiet.id_don_hang_chi_tiet;


--
-- Name: don_hang_id_don_hang_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.don_hang_id_don_hang_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.don_hang_id_don_hang_seq OWNER TO postgres;

--
-- Name: don_hang_id_don_hang_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.don_hang_id_don_hang_seq OWNED BY public.don_hang.id_don_hang;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: gio_hang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.gio_hang (
    id_gio_hang bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id_nguoi_dung integer
);


ALTER TABLE public.gio_hang OWNER TO postgres;

--
-- Name: gio_hang_chi_tiet; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.gio_hang_chi_tiet (
    id_gio_hang_chi_tiet bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    id_san_pham_chi_tiet integer,
    id_gio_hang integer,
    so_luong integer,
    don_gia numeric(15,0)
);


ALTER TABLE public.gio_hang_chi_tiet OWNER TO postgres;

--
-- Name: gio_hang_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.gio_hang_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.gio_hang_id_seq OWNER TO postgres;

--
-- Name: gio_hang_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.gio_hang_id_seq OWNED BY public.gio_hang.id_gio_hang;


--
-- Name: gio_hanh_chi_tiets_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.gio_hanh_chi_tiets_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.gio_hanh_chi_tiets_id_seq OWNER TO postgres;

--
-- Name: gio_hanh_chi_tiets_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.gio_hanh_chi_tiets_id_seq OWNED BY public.gio_hang_chi_tiet.id_gio_hang_chi_tiet;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO postgres;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO postgres;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: kho; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kho (
    id_kho bigint NOT NULL,
    id_san_pham_chi_tiet bigint NOT NULL,
    so_luong_nhap integer NOT NULL,
    ngay_nhap timestamp(0) without time zone DEFAULT '2025-10-17 10:57:48'::timestamp without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.kho OWNER TO postgres;

--
-- Name: kho_id_kho_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kho_id_kho_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kho_id_kho_seq OWNER TO postgres;

--
-- Name: kho_id_kho_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kho_id_kho_seq OWNED BY public.kho.id_kho;


--
-- Name: kich_thuoc; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.kich_thuoc (
    id_kich_thuoc bigint NOT NULL,
    ten_kich_thuoc character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.kich_thuoc OWNER TO postgres;

--
-- Name: kich_thuoc_id_kich_thuoc_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.kich_thuoc_id_kich_thuoc_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kich_thuoc_id_kich_thuoc_seq OWNER TO postgres;

--
-- Name: kich_thuoc_id_kich_thuoc_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.kich_thuoc_id_kich_thuoc_seq OWNED BY public.kich_thuoc.id_kich_thuoc;


--
-- Name: mau; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.mau (
    id_mau bigint NOT NULL,
    ten_mau character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.mau OWNER TO postgres;

--
-- Name: mau_id_mau_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.mau_id_mau_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.mau_id_mau_seq OWNER TO postgres;

--
-- Name: mau_id_mau_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.mau_id_mau_seq OWNED BY public.mau.id_mau;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: nguoi_dung; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.nguoi_dung (
    id_nguoi_dung bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    ngay_sinh date,
    sdt character varying(20),
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.nguoi_dung OWNER TO postgres;

--
-- Name: nguoi_dung_id_nguoi_dung_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.nguoi_dung_id_nguoi_dung_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.nguoi_dung_id_nguoi_dung_seq OWNER TO postgres;

--
-- Name: nguoi_dung_id_nguoi_dung_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.nguoi_dung_id_nguoi_dung_seq OWNED BY public.nguoi_dung.id_nguoi_dung;


--
-- Name: nhap_hang; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.nhap_hang (
    id_nhap_hang bigint NOT NULL,
    ma_nhap_hang character varying(255) NOT NULL,
    ngay_nhap timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.nhap_hang OWNER TO postgres;

--
-- Name: nhap_hang_chi_tiet; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.nhap_hang_chi_tiet (
    id_nhap_hang_chi_tiet bigint NOT NULL,
    id_nhap_hang bigint NOT NULL,
    id_san_pham_chi_tiet bigint NOT NULL,
    so_luong integer NOT NULL,
    don_gia numeric(15,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.nhap_hang_chi_tiet OWNER TO postgres;

--
-- Name: nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq OWNER TO postgres;

--
-- Name: nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq OWNED BY public.nhap_hang_chi_tiet.id_nhap_hang_chi_tiet;


--
-- Name: nhap_hang_id_nhap_hang_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.nhap_hang_id_nhap_hang_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.nhap_hang_id_nhap_hang_seq OWNER TO postgres;

--
-- Name: nhap_hang_id_nhap_hang_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.nhap_hang_id_nhap_hang_seq OWNED BY public.nhap_hang.id_nhap_hang;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO postgres;

--
-- Name: path; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.path (
    id_path bigint NOT NULL,
    ten_path character varying(255) NOT NULL,
    url character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.path OWNER TO postgres;

--
-- Name: path_id_path_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.path_id_path_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.path_id_path_seq OWNER TO postgres;

--
-- Name: path_id_path_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.path_id_path_seq OWNED BY public.path.id_path;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO postgres;

--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: quyen; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.quyen (
    id_quyen bigint NOT NULL,
    ten_quyen character varying(255) NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.quyen OWNER TO postgres;

--
-- Name: quyen_acl; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.quyen_acl (
    id_quyen_acl bigint NOT NULL,
    id_quyen bigint NOT NULL,
    id_path bigint NOT NULL,
    is_read boolean DEFAULT false NOT NULL,
    is_write boolean DEFAULT false NOT NULL,
    is_update boolean DEFAULT false NOT NULL,
    is_delete boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.quyen_acl OWNER TO postgres;

--
-- Name: quyen_acl_id_quyen_acl_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.quyen_acl_id_quyen_acl_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.quyen_acl_id_quyen_acl_seq OWNER TO postgres;

--
-- Name: quyen_acl_id_quyen_acl_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.quyen_acl_id_quyen_acl_seq OWNED BY public.quyen_acl.id_quyen_acl;


--
-- Name: quyen_id_quyen_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.quyen_id_quyen_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.quyen_id_quyen_seq OWNER TO postgres;

--
-- Name: quyen_id_quyen_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.quyen_id_quyen_seq OWNED BY public.quyen.id_quyen;


--
-- Name: quyen_nguoi_dung; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.quyen_nguoi_dung (
    id_quyen_nguoi_dung bigint NOT NULL,
    id_quyen bigint NOT NULL,
    id_nguoi_dung bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.quyen_nguoi_dung OWNER TO postgres;

--
-- Name: quyen_nguoi_dung_id_quyen_nguoi_dung_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.quyen_nguoi_dung_id_quyen_nguoi_dung_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.quyen_nguoi_dung_id_quyen_nguoi_dung_seq OWNER TO postgres;

--
-- Name: quyen_nguoi_dung_id_quyen_nguoi_dung_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.quyen_nguoi_dung_id_quyen_nguoi_dung_seq OWNED BY public.quyen_nguoi_dung.id_quyen_nguoi_dung;


--
-- Name: san_pham; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.san_pham (
    id_san_pham bigint NOT NULL,
    ma_san_pham character varying(255) NOT NULL,
    ten_san_pham character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    mo_ta text,
    gia_niem_yet numeric(12,0),
    gia_ban numeric(12,0),
    trang_thai character varying(255) DEFAULT 'Đang sản xuất'::character varying NOT NULL,
    id_danh_muc_thuong_hieu bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.san_pham OWNER TO postgres;

--
-- Name: san_pham_chi_tiet; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.san_pham_chi_tiet (
    id_san_pham_chi_tiet bigint NOT NULL,
    id_san_pham bigint NOT NULL,
    id_mau bigint NOT NULL,
    id_kich_thuoc bigint NOT NULL,
    so_luong_ton integer DEFAULT 0 NOT NULL,
    ten_san_pham_chi_tiet character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.san_pham_chi_tiet OWNER TO postgres;

--
-- Name: san_pham_chi_tiet_id_san_pham_chi_tiet_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.san_pham_chi_tiet_id_san_pham_chi_tiet_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.san_pham_chi_tiet_id_san_pham_chi_tiet_seq OWNER TO postgres;

--
-- Name: san_pham_chi_tiet_id_san_pham_chi_tiet_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.san_pham_chi_tiet_id_san_pham_chi_tiet_seq OWNED BY public.san_pham_chi_tiet.id_san_pham_chi_tiet;


--
-- Name: san_pham_id_san_pham_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.san_pham_id_san_pham_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.san_pham_id_san_pham_seq OWNER TO postgres;

--
-- Name: san_pham_id_san_pham_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.san_pham_id_san_pham_seq OWNED BY public.san_pham.id_san_pham;


--
-- Name: san_pham_ton_kho; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.san_pham_ton_kho (
    id_san_pham_ton_kho bigint NOT NULL,
    id_san_pham_chi_tiet bigint NOT NULL,
    so_luong_ton integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.san_pham_ton_kho OWNER TO postgres;

--
-- Name: san_pham_ton_kho_id_san_pham_ton_kho_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.san_pham_ton_kho_id_san_pham_ton_kho_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.san_pham_ton_kho_id_san_pham_ton_kho_seq OWNER TO postgres;

--
-- Name: san_pham_ton_kho_id_san_pham_ton_kho_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.san_pham_ton_kho_id_san_pham_ton_kho_seq OWNED BY public.san_pham_ton_kho.id_san_pham_ton_kho;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO postgres;

--
-- Name: thanh_toan; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.thanh_toan (
    id_thanh_toan bigint NOT NULL,
    id_don_hang bigint NOT NULL,
    so_tien numeric(15,0) NOT NULL,
    ten_ngan_hang character varying(100),
    ngay_thanh_toan timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    phuong_thuc character varying(255)
);


ALTER TABLE public.thanh_toan OWNER TO postgres;

--
-- Name: thanh_toan_id_thanh_toan_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.thanh_toan_id_thanh_toan_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.thanh_toan_id_thanh_toan_seq OWNER TO postgres;

--
-- Name: thanh_toan_id_thanh_toan_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.thanh_toan_id_thanh_toan_seq OWNED BY public.thanh_toan.id_thanh_toan;


--
-- Name: thuoc_tinh; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.thuoc_tinh (
    id_thuoc_tinh bigint NOT NULL,
    ten_thuoc_tinh character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.thuoc_tinh OWNER TO postgres;

--
-- Name: thuoc_tinh_chi_tiet; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.thuoc_tinh_chi_tiet (
    id_thuoc_tinh_chi_tiet bigint NOT NULL,
    ten_thuoc_tinh_chi_tiet character varying(255) NOT NULL,
    id_thuoc_tinh bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.thuoc_tinh_chi_tiet OWNER TO postgres;

--
-- Name: thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq OWNER TO postgres;

--
-- Name: thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq OWNED BY public.thuoc_tinh_chi_tiet.id_thuoc_tinh_chi_tiet;


--
-- Name: thuoc_tinh_id_thuoc_tinh_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.thuoc_tinh_id_thuoc_tinh_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.thuoc_tinh_id_thuoc_tinh_seq OWNER TO postgres;

--
-- Name: thuoc_tinh_id_thuoc_tinh_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.thuoc_tinh_id_thuoc_tinh_seq OWNED BY public.thuoc_tinh.id_thuoc_tinh;


--
-- Name: thuong_hieu; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.thuong_hieu (
    id_thuong_hieu bigint NOT NULL,
    ten_thuong_hieu character varying(255) NOT NULL,
    logo_url text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.thuong_hieu OWNER TO postgres;

--
-- Name: thuong_hieu_id_thuong_hieu_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.thuong_hieu_id_thuong_hieu_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.thuong_hieu_id_thuong_hieu_seq OWNER TO postgres;

--
-- Name: thuong_hieu_id_thuong_hieu_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.thuong_hieu_id_thuong_hieu_seq OWNED BY public.thuong_hieu.id_thuong_hieu;


--
-- Name: anh_san_pham id_anh_san_pham; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anh_san_pham ALTER COLUMN id_anh_san_pham SET DEFAULT nextval('public.anh_san_pham_id_anh_san_pham_seq'::regclass);


--
-- Name: banner id_banner; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banner ALTER COLUMN id_banner SET DEFAULT nextval('public.banner_id_banner_seq'::regclass);


--
-- Name: cai_dat id_cai_dat; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cai_dat ALTER COLUMN id_cai_dat SET DEFAULT nextval('public.cai_dat_id_cai_dat_seq'::regclass);


--
-- Name: danh_muc id_danh_muc; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc ALTER COLUMN id_danh_muc SET DEFAULT nextval('public.danh_muc_id_danh_muc_seq'::regclass);


--
-- Name: danh_muc_thuoc_tinh id_danh_muc_thuoc_tinh; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuoc_tinh ALTER COLUMN id_danh_muc_thuoc_tinh SET DEFAULT nextval('public.danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq'::regclass);


--
-- Name: danh_muc_thuong_hieu id_danh_muc_thuong_hieu; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuong_hieu ALTER COLUMN id_danh_muc_thuong_hieu SET DEFAULT nextval('public.danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq'::regclass);


--
-- Name: don_hang id_don_hang; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang ALTER COLUMN id_don_hang SET DEFAULT nextval('public.don_hang_id_don_hang_seq'::regclass);


--
-- Name: don_hang_chi_tiet id_don_hang_chi_tiet; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang_chi_tiet ALTER COLUMN id_don_hang_chi_tiet SET DEFAULT nextval('public.don_hang_chi_tiet_id_don_hang_chi_tiet_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: gio_hang id_gio_hang; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gio_hang ALTER COLUMN id_gio_hang SET DEFAULT nextval('public.gio_hang_id_seq'::regclass);


--
-- Name: gio_hang_chi_tiet id_gio_hang_chi_tiet; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gio_hang_chi_tiet ALTER COLUMN id_gio_hang_chi_tiet SET DEFAULT nextval('public.gio_hanh_chi_tiets_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: kho id_kho; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kho ALTER COLUMN id_kho SET DEFAULT nextval('public.kho_id_kho_seq'::regclass);


--
-- Name: kich_thuoc id_kich_thuoc; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kich_thuoc ALTER COLUMN id_kich_thuoc SET DEFAULT nextval('public.kich_thuoc_id_kich_thuoc_seq'::regclass);


--
-- Name: mau id_mau; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mau ALTER COLUMN id_mau SET DEFAULT nextval('public.mau_id_mau_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: nguoi_dung id_nguoi_dung; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nguoi_dung ALTER COLUMN id_nguoi_dung SET DEFAULT nextval('public.nguoi_dung_id_nguoi_dung_seq'::regclass);


--
-- Name: nhap_hang id_nhap_hang; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nhap_hang ALTER COLUMN id_nhap_hang SET DEFAULT nextval('public.nhap_hang_id_nhap_hang_seq'::regclass);


--
-- Name: nhap_hang_chi_tiet id_nhap_hang_chi_tiet; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nhap_hang_chi_tiet ALTER COLUMN id_nhap_hang_chi_tiet SET DEFAULT nextval('public.nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq'::regclass);


--
-- Name: path id_path; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.path ALTER COLUMN id_path SET DEFAULT nextval('public.path_id_path_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: quyen id_quyen; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen ALTER COLUMN id_quyen SET DEFAULT nextval('public.quyen_id_quyen_seq'::regclass);


--
-- Name: quyen_acl id_quyen_acl; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_acl ALTER COLUMN id_quyen_acl SET DEFAULT nextval('public.quyen_acl_id_quyen_acl_seq'::regclass);


--
-- Name: quyen_nguoi_dung id_quyen_nguoi_dung; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_nguoi_dung ALTER COLUMN id_quyen_nguoi_dung SET DEFAULT nextval('public.quyen_nguoi_dung_id_quyen_nguoi_dung_seq'::regclass);


--
-- Name: san_pham id_san_pham; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham ALTER COLUMN id_san_pham SET DEFAULT nextval('public.san_pham_id_san_pham_seq'::regclass);


--
-- Name: san_pham_chi_tiet id_san_pham_chi_tiet; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_chi_tiet ALTER COLUMN id_san_pham_chi_tiet SET DEFAULT nextval('public.san_pham_chi_tiet_id_san_pham_chi_tiet_seq'::regclass);


--
-- Name: san_pham_ton_kho id_san_pham_ton_kho; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_ton_kho ALTER COLUMN id_san_pham_ton_kho SET DEFAULT nextval('public.san_pham_ton_kho_id_san_pham_ton_kho_seq'::regclass);


--
-- Name: thanh_toan id_thanh_toan; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thanh_toan ALTER COLUMN id_thanh_toan SET DEFAULT nextval('public.thanh_toan_id_thanh_toan_seq'::regclass);


--
-- Name: thuoc_tinh id_thuoc_tinh; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thuoc_tinh ALTER COLUMN id_thuoc_tinh SET DEFAULT nextval('public.thuoc_tinh_id_thuoc_tinh_seq'::regclass);


--
-- Name: thuoc_tinh_chi_tiet id_thuoc_tinh_chi_tiet; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thuoc_tinh_chi_tiet ALTER COLUMN id_thuoc_tinh_chi_tiet SET DEFAULT nextval('public.thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq'::regclass);


--
-- Name: thuong_hieu id_thuong_hieu; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thuong_hieu ALTER COLUMN id_thuong_hieu SET DEFAULT nextval('public.thuong_hieu_id_thuong_hieu_seq'::regclass);


--
-- Data for Name: anh_san_pham; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.anh_san_pham (id_anh_san_pham, anh_url, thu_tu, id_san_pham_chi_tiet, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: banner; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.banner (id_banner, img_url, thu_tu, href, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: cai_dat; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.cai_dat (id_cai_dat, ten_cai_dat, gia_tri, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: danh_muc; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.danh_muc (id_danh_muc, ten_danh_muc, slug, created_at, updated_at) FROM stdin;
1	Vợt cầu lông	vot-cau-long	2025-10-17 12:50:50	2025-10-17 12:50:50
\.


--
-- Data for Name: danh_muc_thuoc_tinh; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.danh_muc_thuoc_tinh (id_danh_muc_thuoc_tinh, id_thuoc_tinh, id_danh_muc, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: danh_muc_thuong_hieu; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.danh_muc_thuong_hieu (id_danh_muc_thuong_hieu, ten_danh_muc_thuong_hieu, slug, mo_ta, id_thuong_hieu, id_danh_muc, created_at, updated_at) FROM stdin;
1	Vợt cầu lông yonex	vot-cau-long-yonex	\N	1	1	2025-10-17 12:52:50	2025-10-17 12:52:50
\.


--
-- Data for Name: don_hang; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.don_hang (id_don_hang, ma_don_hang, id_nguoi_dung, trang_thai_thanh_toan, created_at, updated_at, ngay_dat_hang, phuong_thuc_thanh_toan, trang_thai_don_hang) FROM stdin;
24	DH68F4E5BA38B2D	1	Đã thanh toán	2025-10-19 20:20:58	2025-10-19 20:57:35	2025-10-19 20:20:58.02589	VNPay	Đã nhận
\.


--
-- Data for Name: don_hang_chi_tiet; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.don_hang_chi_tiet (id_don_hang_chi_tiet, id_don_hang, id_san_pham_chi_tiet, so_luong, don_gia, created_at, updated_at) FROM stdin;
16	24	1	2	150000	2025-10-19 20:20:58	2025-10-19 20:20:58
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: gio_hang; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.gio_hang (id_gio_hang, created_at, updated_at, id_nguoi_dung) FROM stdin;
1	\N	\N	1
\.


--
-- Data for Name: gio_hang_chi_tiet; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.gio_hang_chi_tiet (id_gio_hang_chi_tiet, created_at, updated_at, id_san_pham_chi_tiet, id_gio_hang, so_luong, don_gia) FROM stdin;
1	\N	\N	1	1	2	150000
2	\N	\N	2	1	1	150000
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: kho; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kho (id_kho, id_san_pham_chi_tiet, so_luong_nhap, ngay_nhap, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: kich_thuoc; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.kich_thuoc (id_kich_thuoc, ten_kich_thuoc, created_at, updated_at) FROM stdin;
1	4U	2025-10-17 12:51:08	2025-10-17 12:51:08
2	4U6	2025-10-17 12:51:11	2025-10-17 12:51:11
\.


--
-- Data for Name: mau; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.mau (id_mau, ten_mau, created_at, updated_at) FROM stdin;
1	Đỏ	2025-10-17 12:51:00	2025-10-17 12:51:00
2	Xanh	2025-10-17 12:51:03	2025-10-17 12:51:03
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_09_02_000001_create_personal_access_tokens_table	1
5	2025_09_02_181924_create_maus_table	1
6	2025_09_02_181946_create_kich_thuocs_table	1
7	2025_09_03_000101_create_danh_muc_table	1
8	2025_09_03_000102_create_thuong_hieu_table	1
9	2025_09_03_000104_create_thuoc_tinhs_table	1
10	2025_09_03_000105_create_thuong_hieu_danh_mucs_table	1
11	2025_09_03_054723_create_quyen_table	1
12	2025_09_03_062026_create_san_pham_table	1
13	2025_09_09_100717_create_san_pham_chi_tiets_table	1
14	2025_09_09_100718_create_anh_san_phams_table	1
15	2025_09_09_101127_create_san_pham_ton_khos_table	1
16	2025_09_09_102245_create_danh_muc_thuoc_tinhs_table	1
17	2025_09_15_201514_create_thuoc_tinh_chi_tiets_table	1
18	2025_09_21_091426_create_cai_dats_table	1
19	2025_09_28_182514_create_khos_table	1
20	2025_10_10_202608_banner	1
21	2025_10_16_104007_create_gio_hangs_table	1
22	2025_10_16_104021_create_gio_hanh_chi_tiets_table	1
23	2025_10_16_104027_create_don_hangs_table	1
24	2025_10_16_104034_create_don_hang_chi_tiets_table	1
25	2025_10_16_104047_create_thanh_toans_table	1
26	2025_10_16_111721_create_nhap_hangs_table	1
27	2025_10_16_111726_create_nhap_hang_chi_tiets_table	1
\.


--
-- Data for Name: nguoi_dung; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.nguoi_dung (id_nguoi_dung, name, email, ngay_sinh, sdt, email_verified_at, password, remember_token, created_at, updated_at) FROM stdin;
1	Admin User	admin@gmail.com	1993-06-22	0977075924	2025-10-17 10:57:49	$2y$12$payT2L5OfgrYuNFX.LVwpuYRjo87G3oxSbB7BHeZEI2WOpLP0DpXW	aABrpk68DT	2025-10-17 10:57:49	2025-10-17 10:57:49
\.


--
-- Data for Name: nhap_hang; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.nhap_hang (id_nhap_hang, ma_nhap_hang, ngay_nhap, created_at, updated_at) FROM stdin;
1	test	2025-10-18 12:46:39	2025-10-18 19:46:43	2025-10-18 19:46:43
\.


--
-- Data for Name: nhap_hang_chi_tiet; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.nhap_hang_chi_tiet (id_nhap_hang_chi_tiet, id_nhap_hang, id_san_pham_chi_tiet, so_luong, don_gia, created_at, updated_at) FROM stdin;
1	1	1	10	102555.00	2025-10-18 19:46:54	2025-10-18 19:46:54
2	1	3	12	152525.00	2025-10-18 19:47:08	2025-10-18 19:47:08
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: path; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.path (id_path, ten_path, url, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: quyen; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.quyen (id_quyen, ten_quyen, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: quyen_acl; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.quyen_acl (id_quyen_acl, id_quyen, id_path, is_read, is_write, is_update, is_delete, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: quyen_nguoi_dung; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.quyen_nguoi_dung (id_quyen_nguoi_dung, id_quyen, id_nguoi_dung, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: san_pham; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.san_pham (id_san_pham, ma_san_pham, ten_san_pham, slug, mo_ta, gia_niem_yet, gia_ban, trang_thai, id_danh_muc_thuong_hieu, created_at, updated_at) FROM stdin;
1	VNB026679	Vợt cầu lông Yonex Astrox 100 Game VA	vot-cau-long-yonex-astrox-100-game-va	\N	1000000	150000	Đang sản xuất	1	2025-10-17 12:53:12	2025-10-17 12:53:12
\.


--
-- Data for Name: san_pham_chi_tiet; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.san_pham_chi_tiet (id_san_pham_chi_tiet, id_san_pham, id_mau, id_kich_thuoc, so_luong_ton, ten_san_pham_chi_tiet, created_at, updated_at) FROM stdin;
2	1	2	2	0	Vợt cầu lông Yonex Astrox 100 Game VA - Xanh - 4U6	2025-10-18 19:46:26	2025-10-18 19:46:26
3	1	1	1	12	Vợt cầu lông Yonex Astrox 100 Game VA - Đỏ - 4U	2025-10-18 19:46:30	2025-10-18 19:47:08
1	1	2	1	8	Vợt cầu lông Yonex Astrox 100 Game VA - Xanh - 4U	2025-10-17 13:07:53	2025-10-18 19:46:54
\.


--
-- Data for Name: san_pham_ton_kho; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.san_pham_ton_kho (id_san_pham_ton_kho, id_san_pham_chi_tiet, so_luong_ton, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
7HIaj0MvRCpcFNggIozlJ6ev36EaoVQSJLsdENSZ	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36	YTo1OntzOjY6Il90b2tlbiI7czo0MDoieXZRS3JRTzh0dEFXWHlZVVFKV1RyVlI0SDRiM2VkNEZ2VEVra0ZtSiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMwOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvZG9uLWhhbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=	1760882806
3aV60w8O4fbbh8RRlfVZQogY7QLAC8yPbyZcXrTR	1	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQnZQa25TRkxTcWlsSG9WSWxPbHhBcVhCOUtPOEQ0dkYzZVBLc2Y4UyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==	1761233825
\.


--
-- Data for Name: thanh_toan; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.thanh_toan (id_thanh_toan, id_don_hang, so_tien, ten_ngan_hang, ngay_thanh_toan, created_at, updated_at, phuong_thuc) FROM stdin;
11	24	300000	NCB	2025-10-19 20:23:14	2025-10-19 20:21:42	2025-10-19 20:21:42	ATM
\.


--
-- Data for Name: thuoc_tinh; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.thuoc_tinh (id_thuoc_tinh, ten_thuoc_tinh, created_at, updated_at) FROM stdin;
1	commodi	2025-10-17 10:57:49	2025-10-17 10:57:49
2	incidunt	2025-10-17 10:57:49	2025-10-17 10:57:49
3	dignissimos	2025-10-17 10:57:49	2025-10-17 10:57:49
4	voluptatem	2025-10-17 10:57:49	2025-10-17 10:57:49
5	perspiciatis	2025-10-17 10:57:49	2025-10-17 10:57:49
\.


--
-- Data for Name: thuoc_tinh_chi_tiet; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.thuoc_tinh_chi_tiet (id_thuoc_tinh_chi_tiet, ten_thuoc_tinh_chi_tiet, id_thuoc_tinh, created_at, updated_at) FROM stdin;
1	deleniti	1	2025-10-17 10:57:49	2025-10-17 10:57:49
2	quia	1	2025-10-17 10:57:49	2025-10-17 10:57:49
3	pariatur	1	2025-10-17 10:57:49	2025-10-17 10:57:49
4	quis	2	2025-10-17 10:57:49	2025-10-17 10:57:49
5	consequatur	2	2025-10-17 10:57:49	2025-10-17 10:57:49
6	incidunt	2	2025-10-17 10:57:49	2025-10-17 10:57:49
7	incidunt	3	2025-10-17 10:57:49	2025-10-17 10:57:49
8	non	3	2025-10-17 10:57:49	2025-10-17 10:57:49
9	rem	3	2025-10-17 10:57:49	2025-10-17 10:57:49
10	deleniti	4	2025-10-17 10:57:49	2025-10-17 10:57:49
11	ipsam	4	2025-10-17 10:57:49	2025-10-17 10:57:49
12	nostrum	4	2025-10-17 10:57:49	2025-10-17 10:57:49
13	veritatis	5	2025-10-17 10:57:49	2025-10-17 10:57:49
14	at	5	2025-10-17 10:57:49	2025-10-17 10:57:49
15	quod	5	2025-10-17 10:57:49	2025-10-17 10:57:49
\.


--
-- Data for Name: thuong_hieu; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.thuong_hieu (id_thuong_hieu, ten_thuong_hieu, logo_url, created_at, updated_at) FROM stdin;
1	yonex	\N	2025-10-17 12:52:36	2025-10-17 12:52:36
\.


--
-- Name: anh_san_pham_id_anh_san_pham_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.anh_san_pham_id_anh_san_pham_seq', 1, false);


--
-- Name: banner_id_banner_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.banner_id_banner_seq', 1, false);


--
-- Name: cai_dat_id_cai_dat_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.cai_dat_id_cai_dat_seq', 1, false);


--
-- Name: danh_muc_id_danh_muc_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.danh_muc_id_danh_muc_seq', 1, true);


--
-- Name: danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.danh_muc_thuoc_tinh_id_danh_muc_thuoc_tinh_seq', 1, false);


--
-- Name: danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.danh_muc_thuong_hieu_id_danh_muc_thuong_hieu_seq', 1, true);


--
-- Name: don_hang_chi_tiet_id_don_hang_chi_tiet_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.don_hang_chi_tiet_id_don_hang_chi_tiet_seq', 17, true);


--
-- Name: don_hang_id_don_hang_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.don_hang_id_don_hang_seq', 25, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: gio_hang_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.gio_hang_id_seq', 1, true);


--
-- Name: gio_hanh_chi_tiets_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.gio_hanh_chi_tiets_id_seq', 2, true);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: kho_id_kho_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kho_id_kho_seq', 1, false);


--
-- Name: kich_thuoc_id_kich_thuoc_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.kich_thuoc_id_kich_thuoc_seq', 2, true);


--
-- Name: mau_id_mau_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.mau_id_mau_seq', 2, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 27, true);


--
-- Name: nguoi_dung_id_nguoi_dung_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.nguoi_dung_id_nguoi_dung_seq', 1, true);


--
-- Name: nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.nhap_hang_chi_tiet_id_nhap_hang_chi_tiet_seq', 2, true);


--
-- Name: nhap_hang_id_nhap_hang_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.nhap_hang_id_nhap_hang_seq', 1, true);


--
-- Name: path_id_path_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.path_id_path_seq', 1, false);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- Name: quyen_acl_id_quyen_acl_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.quyen_acl_id_quyen_acl_seq', 1, false);


--
-- Name: quyen_id_quyen_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.quyen_id_quyen_seq', 1, false);


--
-- Name: quyen_nguoi_dung_id_quyen_nguoi_dung_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.quyen_nguoi_dung_id_quyen_nguoi_dung_seq', 1, false);


--
-- Name: san_pham_chi_tiet_id_san_pham_chi_tiet_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.san_pham_chi_tiet_id_san_pham_chi_tiet_seq', 3, true);


--
-- Name: san_pham_id_san_pham_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.san_pham_id_san_pham_seq', 9, true);


--
-- Name: san_pham_ton_kho_id_san_pham_ton_kho_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.san_pham_ton_kho_id_san_pham_ton_kho_seq', 1, false);


--
-- Name: thanh_toan_id_thanh_toan_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.thanh_toan_id_thanh_toan_seq', 11, true);


--
-- Name: thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.thuoc_tinh_chi_tiet_id_thuoc_tinh_chi_tiet_seq', 15, true);


--
-- Name: thuoc_tinh_id_thuoc_tinh_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.thuoc_tinh_id_thuoc_tinh_seq', 5, true);


--
-- Name: thuong_hieu_id_thuong_hieu_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.thuong_hieu_id_thuong_hieu_seq', 1, true);


--
-- Name: anh_san_pham anh_san_pham_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anh_san_pham
    ADD CONSTRAINT anh_san_pham_pkey PRIMARY KEY (id_anh_san_pham);


--
-- Name: banner banner_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.banner
    ADD CONSTRAINT banner_pkey PRIMARY KEY (id_banner);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cai_dat cai_dat_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.cai_dat
    ADD CONSTRAINT cai_dat_pkey PRIMARY KEY (id_cai_dat);


--
-- Name: danh_muc danh_muc_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc
    ADD CONSTRAINT danh_muc_pkey PRIMARY KEY (id_danh_muc);


--
-- Name: danh_muc_thuoc_tinh danh_muc_thuoc_tinh_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuoc_tinh
    ADD CONSTRAINT danh_muc_thuoc_tinh_pkey PRIMARY KEY (id_danh_muc_thuoc_tinh);


--
-- Name: danh_muc_thuong_hieu danh_muc_thuong_hieu_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuong_hieu
    ADD CONSTRAINT danh_muc_thuong_hieu_pkey PRIMARY KEY (id_danh_muc_thuong_hieu);


--
-- Name: don_hang_chi_tiet don_hang_chi_tiet_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang_chi_tiet
    ADD CONSTRAINT don_hang_chi_tiet_pkey PRIMARY KEY (id_don_hang_chi_tiet);


--
-- Name: don_hang don_hang_ma_don_hang_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang
    ADD CONSTRAINT don_hang_ma_don_hang_unique UNIQUE (ma_don_hang);


--
-- Name: don_hang don_hang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang
    ADD CONSTRAINT don_hang_pkey PRIMARY KEY (id_don_hang);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: gio_hang gio_hang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gio_hang
    ADD CONSTRAINT gio_hang_pkey PRIMARY KEY (id_gio_hang);


--
-- Name: gio_hang_chi_tiet gio_hanh_chi_tiets_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.gio_hang_chi_tiet
    ADD CONSTRAINT gio_hanh_chi_tiets_pkey PRIMARY KEY (id_gio_hang_chi_tiet);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: kho kho_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kho
    ADD CONSTRAINT kho_pkey PRIMARY KEY (id_kho);


--
-- Name: kich_thuoc kich_thuoc_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kich_thuoc
    ADD CONSTRAINT kich_thuoc_pkey PRIMARY KEY (id_kich_thuoc);


--
-- Name: mau mau_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.mau
    ADD CONSTRAINT mau_pkey PRIMARY KEY (id_mau);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: nguoi_dung nguoi_dung_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nguoi_dung
    ADD CONSTRAINT nguoi_dung_email_unique UNIQUE (email);


--
-- Name: nguoi_dung nguoi_dung_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nguoi_dung
    ADD CONSTRAINT nguoi_dung_pkey PRIMARY KEY (id_nguoi_dung);


--
-- Name: nhap_hang_chi_tiet nhap_hang_chi_tiet_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nhap_hang_chi_tiet
    ADD CONSTRAINT nhap_hang_chi_tiet_pkey PRIMARY KEY (id_nhap_hang_chi_tiet);


--
-- Name: nhap_hang nhap_hang_ma_nhap_hang_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nhap_hang
    ADD CONSTRAINT nhap_hang_ma_nhap_hang_unique UNIQUE (ma_nhap_hang);


--
-- Name: nhap_hang nhap_hang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nhap_hang
    ADD CONSTRAINT nhap_hang_pkey PRIMARY KEY (id_nhap_hang);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: path path_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.path
    ADD CONSTRAINT path_pkey PRIMARY KEY (id_path);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: quyen_acl quyen_acl_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_acl
    ADD CONSTRAINT quyen_acl_pkey PRIMARY KEY (id_quyen_acl);


--
-- Name: quyen_nguoi_dung quyen_nguoi_dung_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_nguoi_dung
    ADD CONSTRAINT quyen_nguoi_dung_pkey PRIMARY KEY (id_quyen_nguoi_dung);


--
-- Name: quyen quyen_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen
    ADD CONSTRAINT quyen_pkey PRIMARY KEY (id_quyen);


--
-- Name: san_pham_chi_tiet san_pham_chi_tiet_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_chi_tiet
    ADD CONSTRAINT san_pham_chi_tiet_pkey PRIMARY KEY (id_san_pham_chi_tiet);


--
-- Name: san_pham san_pham_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham
    ADD CONSTRAINT san_pham_pkey PRIMARY KEY (id_san_pham);


--
-- Name: san_pham_ton_kho san_pham_ton_kho_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_ton_kho
    ADD CONSTRAINT san_pham_ton_kho_pkey PRIMARY KEY (id_san_pham_ton_kho);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: thanh_toan thanh_toan_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thanh_toan
    ADD CONSTRAINT thanh_toan_pkey PRIMARY KEY (id_thanh_toan);


--
-- Name: thuoc_tinh_chi_tiet thuoc_tinh_chi_tiet_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thuoc_tinh_chi_tiet
    ADD CONSTRAINT thuoc_tinh_chi_tiet_pkey PRIMARY KEY (id_thuoc_tinh_chi_tiet);


--
-- Name: thuoc_tinh thuoc_tinh_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thuoc_tinh
    ADD CONSTRAINT thuoc_tinh_pkey PRIMARY KEY (id_thuoc_tinh);


--
-- Name: thuong_hieu thuong_hieu_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thuong_hieu
    ADD CONSTRAINT thuong_hieu_pkey PRIMARY KEY (id_thuong_hieu);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: anh_san_pham anh_san_pham_id_san_pham_chi_tiet_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.anh_san_pham
    ADD CONSTRAINT anh_san_pham_id_san_pham_chi_tiet_foreign FOREIGN KEY (id_san_pham_chi_tiet) REFERENCES public.san_pham_chi_tiet(id_san_pham_chi_tiet) ON DELETE CASCADE;


--
-- Name: danh_muc_thuoc_tinh danh_muc_thuoc_tinh_id_danh_muc_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuoc_tinh
    ADD CONSTRAINT danh_muc_thuoc_tinh_id_danh_muc_foreign FOREIGN KEY (id_danh_muc) REFERENCES public.danh_muc(id_danh_muc) ON DELETE CASCADE;


--
-- Name: danh_muc_thuoc_tinh danh_muc_thuoc_tinh_id_thuoc_tinh_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuoc_tinh
    ADD CONSTRAINT danh_muc_thuoc_tinh_id_thuoc_tinh_foreign FOREIGN KEY (id_thuoc_tinh) REFERENCES public.thuoc_tinh(id_thuoc_tinh) ON DELETE CASCADE;


--
-- Name: danh_muc_thuong_hieu danh_muc_thuong_hieu_id_danh_muc_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuong_hieu
    ADD CONSTRAINT danh_muc_thuong_hieu_id_danh_muc_foreign FOREIGN KEY (id_danh_muc) REFERENCES public.danh_muc(id_danh_muc) ON DELETE CASCADE;


--
-- Name: danh_muc_thuong_hieu danh_muc_thuong_hieu_id_thuong_hieu_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.danh_muc_thuong_hieu
    ADD CONSTRAINT danh_muc_thuong_hieu_id_thuong_hieu_foreign FOREIGN KEY (id_thuong_hieu) REFERENCES public.thuong_hieu(id_thuong_hieu) ON DELETE SET NULL;


--
-- Name: don_hang_chi_tiet don_hang_chi_tiet_id_don_hang_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang_chi_tiet
    ADD CONSTRAINT don_hang_chi_tiet_id_don_hang_foreign FOREIGN KEY (id_don_hang) REFERENCES public.don_hang(id_don_hang) ON DELETE CASCADE;


--
-- Name: don_hang_chi_tiet don_hang_chi_tiet_id_san_pham_chi_tiet_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang_chi_tiet
    ADD CONSTRAINT don_hang_chi_tiet_id_san_pham_chi_tiet_foreign FOREIGN KEY (id_san_pham_chi_tiet) REFERENCES public.san_pham_chi_tiet(id_san_pham_chi_tiet) ON DELETE RESTRICT;


--
-- Name: don_hang don_hang_id_nguoi_dung_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.don_hang
    ADD CONSTRAINT don_hang_id_nguoi_dung_foreign FOREIGN KEY (id_nguoi_dung) REFERENCES public.nguoi_dung(id_nguoi_dung) ON DELETE SET NULL;


--
-- Name: kho kho_id_san_pham_chi_tiet_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.kho
    ADD CONSTRAINT kho_id_san_pham_chi_tiet_foreign FOREIGN KEY (id_san_pham_chi_tiet) REFERENCES public.san_pham_chi_tiet(id_san_pham_chi_tiet) ON DELETE CASCADE;


--
-- Name: nhap_hang_chi_tiet nhap_hang_chi_tiet_id_nhap_hang_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nhap_hang_chi_tiet
    ADD CONSTRAINT nhap_hang_chi_tiet_id_nhap_hang_foreign FOREIGN KEY (id_nhap_hang) REFERENCES public.nhap_hang(id_nhap_hang) ON DELETE CASCADE;


--
-- Name: nhap_hang_chi_tiet nhap_hang_chi_tiet_id_san_pham_chi_tiet_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.nhap_hang_chi_tiet
    ADD CONSTRAINT nhap_hang_chi_tiet_id_san_pham_chi_tiet_foreign FOREIGN KEY (id_san_pham_chi_tiet) REFERENCES public.san_pham_chi_tiet(id_san_pham_chi_tiet) ON DELETE RESTRICT;


--
-- Name: quyen_acl quyen_acl_id_path_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_acl
    ADD CONSTRAINT quyen_acl_id_path_foreign FOREIGN KEY (id_path) REFERENCES public.path(id_path) ON DELETE CASCADE;


--
-- Name: quyen_acl quyen_acl_id_quyen_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_acl
    ADD CONSTRAINT quyen_acl_id_quyen_foreign FOREIGN KEY (id_quyen) REFERENCES public.quyen(id_quyen) ON DELETE CASCADE;


--
-- Name: quyen_nguoi_dung quyen_nguoi_dung_id_nguoi_dung_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_nguoi_dung
    ADD CONSTRAINT quyen_nguoi_dung_id_nguoi_dung_foreign FOREIGN KEY (id_nguoi_dung) REFERENCES public.nguoi_dung(id_nguoi_dung) ON DELETE CASCADE;


--
-- Name: quyen_nguoi_dung quyen_nguoi_dung_id_quyen_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.quyen_nguoi_dung
    ADD CONSTRAINT quyen_nguoi_dung_id_quyen_foreign FOREIGN KEY (id_quyen) REFERENCES public.quyen(id_quyen) ON DELETE CASCADE;


--
-- Name: san_pham_chi_tiet san_pham_chi_tiet_id_kich_thuoc_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_chi_tiet
    ADD CONSTRAINT san_pham_chi_tiet_id_kich_thuoc_foreign FOREIGN KEY (id_kich_thuoc) REFERENCES public.kich_thuoc(id_kich_thuoc) ON DELETE CASCADE;


--
-- Name: san_pham_chi_tiet san_pham_chi_tiet_id_mau_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_chi_tiet
    ADD CONSTRAINT san_pham_chi_tiet_id_mau_foreign FOREIGN KEY (id_mau) REFERENCES public.mau(id_mau) ON DELETE CASCADE;


--
-- Name: san_pham_chi_tiet san_pham_chi_tiet_id_san_pham_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_chi_tiet
    ADD CONSTRAINT san_pham_chi_tiet_id_san_pham_foreign FOREIGN KEY (id_san_pham) REFERENCES public.san_pham(id_san_pham) ON DELETE CASCADE;


--
-- Name: san_pham san_pham_id_danh_muc_thuong_hieu_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham
    ADD CONSTRAINT san_pham_id_danh_muc_thuong_hieu_foreign FOREIGN KEY (id_danh_muc_thuong_hieu) REFERENCES public.danh_muc_thuong_hieu(id_danh_muc_thuong_hieu) ON DELETE CASCADE;


--
-- Name: san_pham_ton_kho san_pham_ton_kho_id_san_pham_chi_tiet_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.san_pham_ton_kho
    ADD CONSTRAINT san_pham_ton_kho_id_san_pham_chi_tiet_foreign FOREIGN KEY (id_san_pham_chi_tiet) REFERENCES public.san_pham_chi_tiet(id_san_pham_chi_tiet) ON DELETE CASCADE;


--
-- Name: thanh_toan thanh_toan_id_don_hang_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thanh_toan
    ADD CONSTRAINT thanh_toan_id_don_hang_foreign FOREIGN KEY (id_don_hang) REFERENCES public.don_hang(id_don_hang) ON DELETE CASCADE;


--
-- Name: thuoc_tinh_chi_tiet thuoc_tinh_chi_tiet_id_thuoc_tinh_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.thuoc_tinh_chi_tiet
    ADD CONSTRAINT thuoc_tinh_chi_tiet_id_thuoc_tinh_foreign FOREIGN KEY (id_thuoc_tinh) REFERENCES public.thuoc_tinh(id_thuoc_tinh) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict Q0D6xhCAOK3fhcRZPQ5hwjlcbLjdEakkr0nfUe82SQHXOkUkoIAFv3GTjMF0ZGe

