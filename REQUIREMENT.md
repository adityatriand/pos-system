# Recruitment Test PT. Max System Management

## Project Overview
Sistem POS (Point of Sale) sederhana dengan berbagai modul manajemen dan dashboard analitik.

## 1. Modul Sistem POS

### Core Modules:
- **Data Cabang** - Manajemen informasi cabang
- **Data Karyawan** - Manajemen data karyawan
- **Data Barang & Minuman** - Katalog produk dan inventory
- **Data Transaksi Penjualan** - Proses transaksi penjualan
- **Riwayat Penjualan** - Catatan histori penjualan
- **Dashboard** - Panel analitik dan summary

## 2. Fitur Riwayat Penjualan

### Features:
- **Filter by Cabang dan Tanggal** - Filterisasi berdasarkan cabang dan rentang tanggal
- **Export PDF** - Cetak daftar penjualan dalam format PDF

## 3. Dashboard Analytics

### Dashboard Summary (dengan filter tanggal range):
- **Total Pendapatan** - Total pendapatan seluruh cabang
- **Total Service Charge** - Total service charge seluruh cabang  
- **Top 5 Barang Terlaris** - Produk dengan penjualan tertinggi
- **Ranking Cabang** - Total pendapatan per cabang (diurutkan tertinggi ke terendah)

## 4. Business Rules

### Pricing:
- **Service Charge** - Setiap penjualan dikenakan service charge tambahan 5%

## 5. Technical Stack

### Backend:
- **Framework**: Laravel (versi terbaru)
- **Database**: MySQL

### Frontend:
- **Framework**: Vue.js
- **Language**: TypeScript
- **CSS Framework**: Tailwind CSS

## 6. Deployment

### Requirements:
- **Containerization**: Docker setup untuk deployment

## 7. Documentation

### Deliverables:
- **README.md** - Panduan menjalankan project