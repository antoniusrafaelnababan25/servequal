<div align="center">
  
# 📊 SERVQUAL MONITORING SYSTEM

[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg?style=for-the-badge&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-blue.svg?style=for-the-badge&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg?style=for-the-badge&logo=mysql)](https://mysql.com)
[![Status](https://img.shields.io/badge/Status-Production-green.svg?style=for-the-badge)](https://github.com/antoniusrafaelnababan25/servequal)
[![MIT License](https://img.shields.io/badge/License-MIT-yellow.svg?style=for-the-badge)](LICENSE)

**Aplikasi Monitoring Kualitas Layanan dengan Metode SERVQUAL**  
*Mengukur kepuasan pelanggan melalui analisis 5 dimensi kualitas layanan*

</div>

---

## 📋 DAFTAR ISI

- [Tentang Project](#-tentang-project)
- [Fitur Lengkap](#-fitur-lengkap)
- [5 Dimensi SERVQUAL](#-5-dimensi-servqual)
- [Teknologi](#-teknologi)
- [Instalasi Cepat](#-instalasi-cepat)
- [Konfigurasi Database](#-konfigurasi-database)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Struktur Database](#-struktur-database)
- [Penggunaan Aplikasi](#-penggunaan-aplikasi)
- [API Endpoints](#-api-endpoints)
- [Troubleshooting](#-troubleshooting)
- [Deployment](#-deployment)
- [Lisensi](#-lisensi)

---

## 🎯 TENTANG PROJECT

**SERVQUAL Monitoring System** adalah aplikasi web berbasis Laravel 12 yang dirancang untuk membantu organisasi mengukur dan menganalisis kualitas layanan menggunakan metode **SERVQUAL (Service Quality)** yang dikembangkan oleh Parasuraman, Zeithaml, dan Berry.

Metode SERVQUAL mengukur **GAP** (kesenjangan) antara:
- **Harapan (Expectation)** : Tingkat layanan yang diharapkan pelanggan
- **Persepsi (Perception)** : Tingkat layanan yang benar-benar diterima pelanggan

**Rumus GAP:** `GAP = Persepsi - Harapan`

- **GAP positif** ( > 0 ) : Layanan melebihi harapan (Sangat Baik)
- **GAP nol** ( = 0 ) : Layanan sesuai harapan (Baik)
- **GAP negatif** ( < 0 ) : Layanan di bawah harapan (Perlu Perbaikan)

---

## ✨ FITUR LENGKAP

### 👥 Manajemen Responden
| Fitur | Deskripsi |
|-------|-----------|
| CRUD Lengkap | Tambah, edit, hapus, lihat data responden |
| Import Excel | Import data responden massal dari file Excel |
| Export Data | Export ke format Excel, CSV, atau PDF |
| Search & Filter | Cari berdasarkan nama, umur, pekerjaan, status |
| Kode Unik | Generate kode unik otomatis untuk setiap responden |
| Status Tracking | Lacak status pengisian (pending/completed/expired) |

### 📝 Kuesioner SERVQUAL
| Fitur | Deskripsi |
|-------|-----------|
| 22 Pernyataan | Mengikuti standar SERVQUAL original |
| Skala Likert 1-7 | 1=Sangat Tidak Setuju, 7=Sangat Setuju |
| Penilaian Ganda | Harapan dan persepsi dinilai terpisah |
| Progress Bar | Indikator progress pengisian kuesioner |
| Auto-save | Data tersimpan otomatis per halaman |

### 📊 Analisis & Pelaporan
| Fitur | Deskripsi |
|-------|-----------|
| Gap Analysis | Perhitungan gap (P - E) otomatis real-time |
| Per Dimensi | Analisis detail per 5 dimensi SERVQUAL |
| Visualisasi Grafik | Grafik batang, garis, radar, dan pie chart |
| Export Laporan | Generate laporan PDF, Excel, atau CSV |
| Rekomendasi | Rekomendasi perbaikan berdasarkan gap terbesar |

### 🛡️ Admin Panel
| Fitur | Deskripsi |
|-------|-----------|
| Dashboard | Ringkasan statistik real-time (total responden, rata-rata gap, tren) |
| Manajemen User | CRUD user, role & permission (admin/staff/viewer) |
| Activity Log | Catatan semua aktivitas pengguna |
| Backup Database | Backup & restore database otomatis |
| Pengaturan Sistem | Konfigurasi dinamis (periode laporan, skor minimal/maksimal) |

### 🔔 Notifikasi
| Fitur | Deskripsi |
|-------|-----------|
| Email Notifikasi | Pengingat pengisian kuesioner via email |
| Real-time Toast | Notifikasi sukses/error/warning |
| Weekly Report | Laporan mingguan otomatis ke email admin |

---

## 📐 5 DIMENSI SERVQUAL

| Dimensi | Kode | Penjelasan | Contoh Pernyataan |
|---------|------|------------|-------------------|
| **Tangible** (Bukti Fisik) | TAN | Penampilan fasilitas fisik, peralatan, dan karyawan | "Perusahaan memiliki peralatan yang modern" |
| **Reliability** (Keandalan) | REL | Kemampuan memberikan layanan yang dijanjikan dengan tepat dan andal | "Perusahaan memberikan layanan sesuai janji yang diberikan" |
| **Responsiveness** (Daya Tanggap) | RES | Kesediaan membantu pelanggan dan memberikan layanan yang cepat | "Karyawan cepat tanggap dalam menangani keluhan pelanggan" |
| **Assurance** (Jaminan) | ASS | Pengetahuan, kesopanan karyawan, dan kemampuan menumbuhkan kepercayaan | "Karyawan membuat pelanggan merasa aman selama bertransaksi" |
| **Empathy** (Empati) | EMP | Perhatian individual yang diberikan perusahaan kepada pelanggan | "Perusahaan memiliki jam operasional yang nyaman bagi pelanggan" |

---

## 🛠️ TEKNOLOGI

### Backend
- Laravel 12.x (Stable Release)
- PHP 8.4 (Latest Stable)
- MySQL 8.0+ / MariaDB 11.0+
- Laravel Sanctum (Authentication API)
- Laravel Excel (Import/Export)
- DomPDF / barryvdh/laravel-dompdf
- Laravel Reverb (WebSocket - Optional)

### Frontend
- Blade Template Engine with Laravel 12 Features
- Bootstrap 5.3
- Tailwind CSS 3.x
- Alpine.js 3.x (Laravel 12 Default)
- Vite (Build Tool - Laravel 12 Default)
- Chart.js / ApexCharts
- DataTables (Server-side)

### Devops & Tools
- Git & GitHub
- Composer 2.x
- NPM / Yarn
- Laragon (Local Development - PHP 8.4 Ready)
- VS Code with Laravel Extension Pack

---

## ⚡ INSTALASI CEPAT

### Persyaratan Sistem (Update untuk PHP 8.4 & Laravel 12)

| Komponen | Minimal | Rekomendasi |
|----------|---------|-------------|
| PHP | 8.4.0 | 8.4.4+ |
| Composer | 2.5+ | 2.8+ |
| MySQL | 8.0+ | 8.4+ |
| Node.js | 20.x+ | 22.x+ |
| Laragon | 6.0+ (dengan PHP 8.4) | Terbaru |
| Ekstensi PHP | BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, GD, Zip | Semua terinstall |

### Langkah-langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/antoniusrafaelnababan25/servequal.git
cd servequal

# 2. Install PHP dependencies (Laravel 12 & PHP 8.4 compatible)
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Create database (via phpMyAdmin atau CLI)
# CREATE DATABASE servqual_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 6. Jalankan migrasi database
php artisan migrate

# 7. Isi data awal (seeder)
php artisan db:seed

# 8. Buat symbolic link untuk storage
php artisan storage:link

# 9. Install frontend dependencies dengan Vite (Laravel 12 default)
npm install
npm run build

# 10. Jalankan development server
php artisan serve
