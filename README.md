# 📊 SERVQUAL MONITORING SYSTEM

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4+-blue.svg?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg?style=for-the-badge&logo=mysql)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-06B6D4.svg?style=for-the-badge&logo=tailwindcss)

**Aplikasi Monitoring Kualitas Layanan dengan Metode SERVQUAL**  
*Mengukur kepuasan pelanggan melalui analisis 5 dimensi kualitas layanan*

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Status](https://img.shields.io/badge/Status-Production-green.svg)]()
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)]()

</div>

---

## 📋 Daftar Isi

- [Tentang Project](#tentang-project)
- [Fitur Lengkap](#fitur-lengkap)
- [5 Dimensi SERVQUAL](#5-dimensi-servqual)
- [Teknologi](#teknologi)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Database](#struktur-database)
- [API Endpoints](#api-endpoints)
- [Troubleshooting](#troubleshooting)
- [Lisensi](#lisensi)

---

## 🎯 Tentang Project

**SERVQUAL Monitoring System** adalah aplikasi web berbasis Laravel 12 untuk mengukur kualitas layanan menggunakan metode **SERVQUAL (Service Quality)**.

### Konsep Dasar

| Komponen | Deskripsi |
|----------|-----------|
| **Harapan (E)** | Tingkat layanan yang diharapkan pelanggan |
| **Persepsi (P)** | Tingkat layanan yang benar-benar diterima |
| **GAP** | Selisih antara Persepsi dan Harapan (`P - E`) |

### Interpretasi GAP

| Nilai GAP | Status | Tindakan |
|-----------|--------|----------|
| GAP > 0 | ✅ Layanan melebihi harapan | Pertahankan |
| GAP = 0 | ⚖️ Layanan sesuai harapan | Evaluasi |
| GAP < 0 | ❌ Layanan di bawah harapan | Perbaiki |

---

## ✨ Fitur Lengkap

### 👥 Manajemen Responden
- CRUD lengkap data responden
- Import/Export Excel
- Kode unik otomatis
- Status tracking (pending/completed)

### 📝 Kuesioner SERVQUAL
- 22 pernyataan standar
- Skala Likert 1-7
- Penilaian harapan & persepsi
- Progress bar & auto-save

### 📊 Analisis & Pelaporan
- Gap analysis otomatis
- Analisis per dimensi
- Grafik (batang, radar, pie)
- Export PDF/Excel

---

## 📐 5 Dimensi SERVQUAL

| Dimensi | Kode | Item | Penjelasan |
|---------|------|------|-------------|
| Tangible | TAN | 4 | Bukti fisik perusahaan |
| Reliability | REL | 5 | Keandalan layanan |
| Responsiveness | RES | 4 | Daya tanggap karyawan |
| Assurance | ASS | 4 | Jaminan & kepercayaan |
| Empathy | EMP | 5 | Perhatian individual |
| **Total** | | **22** | |

---

## 🛠️ Teknologi

| Kategori | Teknologi | Versi |
|----------|-----------|-------|
| Framework | Laravel | 12.x |
| Bahasa | PHP | 8.4+ |
| Database | MySQL | 8.0+ |
| CSS | Tailwind CSS | 3.x |
| JavaScript | Alpine.js | 3.x |
| Build Tool | Vite | 5.x |

---

## 💻 Persyaratan Sistem

| Komponen | Minimal |
|----------|---------|
| PHP | 8.4.0+ |
| Composer | 2.5+ |
| MySQL | 8.0+ |
| Node.js | 20.x+ |

---

## ⚡ Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/antoniusrafaelnababan25/servequal.git
cd servequal
2. Install Dependencies
bash
composer install
npm install
3. Environment Setup
bash
cp .env.example .env
php artisan key:generate
4. Database Setup
bash
# Buat database
mysql -u root -p
CREATE DATABASE servqual_db;
EXIT;

# Konfigurasi .env
DB_DATABASE=servqual_db
DB_USERNAME=root
DB_PASSWORD=
5. Migrasi & Seeder
bash
php artisan migrate --seed
php artisan storage:link
6. Build & Run
bash
npm run build
php artisan serve
Akses: http://localhost:8000

Default Login:

Email: admin@servqual.com

Password: password

⚙️ Konfigurasi .env
env
APP_NAME="SERVQUAL Monitoring System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=servqual_db
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
🚀 Menjalankan Aplikasi
Development Mode
bash
# Terminal 1
php artisan serve

# Terminal 2
npm run dev
Production Mode
bash
php artisan optimize
npm run build
php artisan serve --host=0.0.0.0 --port=8000
Queue Worker (Email)
bash
php artisan queue:work
📊 Struktur Database
Tabel Utama
sql
-- Tabel responden
CREATE TABLE responden (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode_unik VARCHAR(50) UNIQUE,
    nama_lengkap VARCHAR(100),
    email VARCHAR(100),
    telepon VARCHAR(20),
    usia INT,
    pekerjaan VARCHAR(50),
    pendidikan VARCHAR(50),
    created_at TIMESTAMP
);

-- Tabel dimensi
CREATE TABLE dimensi (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode VARCHAR(10) UNIQUE,
    nama VARCHAR(50),
    deskripsi TEXT,
    urutan INT
);

-- Tabel pernyataan
CREATE TABLE pernyataan (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    dimensi_id BIGINT,
    kode VARCHAR(10),
    pernyataan TEXT,
    FOREIGN KEY (dimensi_id) REFERENCES dimensi(id)
);

-- Tabel jawaban
CREATE TABLE jawaban (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    responden_id BIGINT,
    pernyataan_id BIGINT,
    jenis ENUM('harapan', 'persepsi'),
    nilai INT CHECK (nilai BETWEEN 1 AND 7),
    FOREIGN KEY (responden_id) REFERENCES responden(id),
    FOREIGN KEY (pernyataan_id) REFERENCES pernyataan(id)
);
Data Dimensi Awal
sql
INSERT INTO dimensi (kode, nama, urutan) VALUES
('TAN', 'Tangible (Bukti Fisik)', 1),
('REL', 'Reliability (Keandalan)', 2),
('RES', 'Responsiveness (Daya Tanggap)', 3),
('ASS', 'Assurance (Jaminan)', 4),
('EMP', 'Empathy (Empati)', 5);
🔌 API Endpoints
Responden
Method	Endpoint	Deskripsi
GET	/api/responden	List responden
POST	/api/responden	Tambah responden
GET	/api/responden/{id}	Detail responden
PUT	/api/responden/{id}	Update responden
DELETE	/api/responden/{id}	Hapus responden
Kuesioner
Method	Endpoint	Deskripsi
GET	/api/kuesioner/{token}	Get kuesioner
POST	/api/kuesioner/{token}/jawaban	Simpan jawaban
GET	/api/kuesioner/{token}/progress	Get progress
Laporan
Method	Endpoint	Deskripsi
GET	/api/laporan/gap	Laporan GAP
GET	/api/laporan/dimensi	Laporan per dimensi
GET	/api/laporan/export/pdf	Export PDF
GET	/api/laporan/export/excel	Export Excel
🐛 Troubleshooting
1. Error "Class not found"
bash
composer dump-autoload
php artisan optimize:clear
2. Error "Connection refused" Database
bash
# Cek service MySQL
sudo systemctl start mysql
# Atau restart Laragon
3. Error "Storage link already exists"
bash
rm -rf public/storage
php artisan storage:link
4. Error "Vite manifest not found"
bash
npm install
npm run build
5. Reset Database
bash
php artisan migrate:fresh --seed
6. Clear All Cache
bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
📁 Struktur Folder
text
servqual/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── RespondenController.php
│   │   │   ├── KuesionerController.php
│   │   │   ├── LaporanController.php
│   │   │   └── AuthController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── Responden.php
│   │   ├── Dimensi.php
│   │   ├── Pernyataan.php
│   │   └── Jawaban.php
│   └── Imports/
│       └── RespondenImport.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── responden/
│   │   ├── kuesioner/
│   │   └── laporan/
│   └── css/
│       └── app.css
├── routes/
│   ├── web.php
│   └── api.php
└── public/
    └── storage/
🤝 Kontribusi
Fork repository

Buat branch fitur (git checkout -b fitur-baru)

Commit perubahan (git commit -m 'Menambah fitur')

Push ke branch (git push origin fitur-baru)

Buat Pull Request

📄 Lisensi
Project ini dilisensikan di bawah MIT License.

<div align="center">
Dibuat dengan ❤️ oleh Antonius Rafael Nababan

https://img.shields.io/badge/GitHub-antoniusrafaelnababan25-181717?style=for-the-badge&logo=github

</div> ```
