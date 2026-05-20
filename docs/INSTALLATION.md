# Tutorial Instalasi Project RS Unknown

RS Unknown adalah aplikasi Laravel dengan Livewire untuk sistem manajemen transaksi dan voucher. Berikut adalah panduan lengkap untuk menginstal project ini.

## Prasyarat Sistem

- **PHP**: versi 8.3 atau lebih tinggi
- **Composer**: untuk manajemen dependency PHP
- **Node.js & npm**: untuk frontend assets
- **PostgreSQL**: database yang digunakan oleh project
- **Git**: untuk version control

## Langkah-Langkah Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd rs_<name>
```

### 2. Install PHP Dependencies

Gunakan Composer untuk menginstal semua dependency PHP:

```bash
composer install
```

### 3. Setup Environment File

Copy file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Edit file `.env` sesuai dengan konfigurasi lokal Anda:

```env
# Aplikasi
APP_NAME=rs_<name>
APP_ENV=local
APP_KEY=  # Akan di-generate di langkah berikutnya
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=rs_<name>
DB_USERNAME=rs_<name>
DB_PASSWORD=1

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache & Queue
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 4. Generate Application Key

Jalankan command untuk generate APP_KEY:

```bash
php artisan key:generate
```

### 5. Buat Database

Pastikan PostgreSQL server sudah berjalan, kemudian buat database:

```bash
createdb -U rs_<name> rs_<name>
```

Atau gunakan PostgreSQL client sesuai preferensi Anda.

### 6. Migrasi Database

Jalankan migration untuk membuat tabel-tabel database:

```bash
php artisan migrate
```

### 7. Seed Database (Opsional)

Jika ada seeder untuk data awal:

```bash
php artisan db:seed
```

### 8. Install Frontend Dependencies

```bash
npm install
```

### 9. Build Frontend Assets

```bash
npm run build
```

Untuk development dengan live reload:

```bash
npm run dev
```

### 10. Generate Symbolic Link Storage

```bash
php artisan storage:link
```

## Menjalankan Aplikasi

### Development Server

Buka dua terminal di directory project:

**Terminal 1 - PHP Server:**
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

**Terminal 2 - Frontend Watch (untuk development):**
```bash
npm run dev
```

## Konfigurasi Tambahan

### Fortify Authentication

Project menggunakan Laravel Fortify untuk autentikasi. Setup sudah otomatis konfigurasi di provider. Anda bisa mengakses:
- Login: `/login`
- Register: `/register`
- Profile: `/user/profile-information`
- Security: `/user/two-factor-authentication`

### Permission & Role

Project menggunakan Spatie Permission untuk manajemen role dan permission. Default roles yang tersedia:
- **admin**: Full akses
- **marketing**: Akses ke dashboard dan management
- **cashier**: Akses ke transaksi

Buat roles & permissions melalui command atau seeder:

```bash
php artisan tinker
>>> \Spatie\Permission\Models\Role::create(['name' => 'admin']);
>>> \Spatie\Permission\Models\Role::create(['name' => 'marketing']);
>>> \Spatie\Permission\Models\Role::create(['name' => 'cashier']);
```

### Activity Logging

Project menggunakan Spatie Activity Log untuk mencatat setiap aktivitas. Semua perubahan data akan tercatat di database.

## Testing

Jalankan test suite:

```bash
./vendor/bin/pest
```

Untuk test dengan coverage:

```bash
./vendor/bin/pest --coverage
```

## Troubleshooting

### 1. Database Connection Error

Pastikan:
- PostgreSQL server berjalan
- Database credentials di `.env` sesuai
- Database sudah dibuat

### 2. Permission Denied pada Storage

Jalankan:
```bash
chmod -R 775 storage bootstrap/cache
```

### 3. Frontend Assets Tidak Muncul

Rebuild frontend:
```bash
npm run build
```

### 4. Memory Limit Error

Update file `.env` atau gunakan:
```bash
php -d memory_limit=-1 artisan migrate
```

## Informasi Lebih Lanjut

- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Fortify Documentation](https://laravel.com/docs/fortify)
- [Spatie Permission](https://spatie.be/docs/laravel-permission/v5/introduction)

---

**Last Updated**: May 2026
