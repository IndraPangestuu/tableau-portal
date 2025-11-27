# Panduan Instalasi - Portal Dashboard Korlantas

Portal web untuk menampilkan dashboard Tableau dengan fitur autentikasi dan manajemen menu.

## Persyaratan Sistem

- PHP >= 7.3 atau 8.0+
- Composer
- MySQL / MariaDB
- Web Server (Apache/Nginx) atau XAMPP
- Tableau Server dengan Trusted Authentication

## Langkah Instalasi

### 1. Clone atau Download Project

```bash
cd C:\xampp\htdocs
git clone <repository-url> tableau-portal
cd tableau-portal
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

Copy file environment:
```bash
copy .env.example .env
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tableau_portal
DB_USERNAME=root
DB_PASSWORD=
```

Buat database di MySQL:
```sql
CREATE DATABASE tableau_portal;
```

### 5. Konfigurasi Tableau Server

Edit file `.env` dan tambahkan konfigurasi Tableau:

```env
# Tableau Server URL
TABLEAU_SERVER=http://103.154.174.60

# API Version (sesuaikan dengan versi Tableau Server)
TABLEAU_API_VERSION=3.8

# Site ID (kosongkan untuk default site)
TABLEAU_SITE_ID=

# Credentials untuk REST API (fetch daftar dashboard)
TABLEAU_ADMIN_USERNAME=admin_tableau
TABLEAU_ADMIN_PASSWORD=password_admin

# Username untuk Trusted Auth (embed dashboard)
TABLEAU_VIEWER_USERNAME=korlantas_viewer_2
```

### 6. Jalankan Migration

```bash
php artisan migrate
```

### 7. Buat User Admin

```bash
php artisan tinker
```

Lalu jalankan:
```php
\App\Models\User::create([
    'name' => 'Administrator',
    'username' => 'admin',
    'nrp' => '12345',
    'password' => bcrypt('password123'),
    'role' => 'admin'
]);
```

Ketik `exit` untuk keluar.

### 8. Jalankan Aplikasi

```bash
php artisan serve
```

Akses aplikasi di: `http://127.0.0.1:8000`

## Login

- **Username:** admin
- **Password:** password123

## Konfigurasi Tableau Server

### Trusted Authentication

Agar embed dashboard berfungsi, IP server web harus didaftarkan sebagai Trusted Host di Tableau Server:

1. Login ke Tableau Server sebagai admin
2. Buka **Settings** → **Trusted Authentication**
3. Tambahkan IP server web (contoh: `192.168.1.100`)
4. Restart Tableau Server jika diperlukan

### Format View Path

Saat menambahkan menu dashboard, gunakan format path:
```
/views/[WorkbookName]/[ViewName]
```

Contoh:
- `/views/SUMMARYDAKGARLANTASv2/Dashboard17`
- `/views/home/01_SummaryDAKGARLANTAS3`

## Struktur Menu

### Admin Panel
- **Kelola User** - CRUD user dengan role admin/user
- **Kelola Menu** - Tambah/edit/hapus menu dashboard

### User Biasa
- Hanya bisa melihat dashboard yang sudah dikonfigurasi

## Troubleshooting

### Dashboard tidak muncul

1. Cek log error:
   ```powershell
   Get-Content storage/logs/laravel.log -Tail 50
   ```

2. Pastikan IP server sudah terdaftar di Tableau Trusted Host

3. Pastikan username viewer punya akses ke dashboard

### Error "Trusted Auth gagal"

- IP server belum terdaftar sebagai Trusted Host di Tableau Server
- Cek konfigurasi `TABLEAU_VIEWER_USERNAME` di `.env`

### Tidak bisa fetch daftar dashboard

- Cek `TABLEAU_ADMIN_USERNAME` dan `TABLEAU_ADMIN_PASSWORD`
- Pastikan user admin punya akses REST API
- Cek `TABLEAU_SITE_ID` jika menggunakan multi-site

### Clear Cache

Jika ada perubahan konfigurasi:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Perintah Berguna

```bash
# Jalankan server development
php artisan serve

# Clear semua cache
php artisan optimize:clear

# Lihat daftar routes
php artisan route:list

# Masuk ke tinker (REPL)
php artisan tinker

# Rollback migration
php artisan migrate:rollback

# Fresh migration (hapus semua tabel)
php artisan migrate:fresh
```

## Lisensi

MIT License
