# DonasiKita - Web Donasi PHP

## Cara Setup

### 1. Import Database
Buka phpMyAdmin atau MySQL CLI, lalu jalankan:
```sql
source /path/to/donasi/database.sql
```

### 2. Konfigurasi Database
Edit `config/db.php` sesuai kredensial MySQL kamu:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'donasi_db');
```

### 3. Jalankan via XAMPP/Laragon
Letakkan folder `donasi/` di dalam `htdocs/` (XAMPP) atau `www/` (Laragon), lalu akses:
```
http://localhost/donasi/
```

## Akun Default
| Role  | Email              | Password  |
|-------|--------------------|-----------|
| Admin | admin@donasi.com   | password  |

> Catatan: Password default di database.sql adalah hash dari "password". Ganti setelah login pertama.

## Alur Penggunaan
1. User buka `http://localhost/donasi/`
2. Register / Login
3. Pilih campaign dari daftar
4. Klik "Donasi Uang" atau "Donasi Barang"
5. Isi form dan submit
6. Halaman sukses tampil status "Diproses"
7. Cek riwayat di menu "Riwayat"
8. Admin konfirmasi di `/donasi/admin/`

## Struktur Folder
```
donasi/
├── config/db.php          # Koneksi & helper
├── auth/                  # Login, Register, Logout
├── campaigns/             # List & Detail campaign
├── donate/                # Form, Submit, Sukses
├── history/               # Riwayat donasi user
├── admin/                 # Dashboard & kelola campaign
├── includes/              # Navbar & footer
├── assets/style.css       # Styling
└── database.sql           # Schema & data awal
```
"# project_donation" 
# project_donation
