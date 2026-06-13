# Test Bimasakti Fullstack

## Cara Menjalankan Aplikasi
0. Pastikan program berikut telah terinstall:
- PHP 8.2+
- Composer
- MySQL
- NPM 
1. Clone atau download source code berikut
2. Buka folder
3. Run script berikut di terminal untuk install:
```
composer install
npm install
```
4. Run script untuk membuat file .env:
```
cp .env.example .env
```
5. Buka file .env, tambahkan kode berikut, ubah jika sudah ada:
```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=test_bimasakti
DB_USERNAME=<username database>
DB_PASSWORD=<password database>

BASE_URL=<sesuai base_url pada PDF tes>
SECRET=<sesuai base_url pada PDF tes>
```
6. Run script untuk key, storage, dan database migration:
```
php artisan key:generate
php artisan storage:link
php artisan migrate 
```
7. (Opsional) Bisa import file .sql yang ada agar db terisi
8. Run server laravel
```
php artisan serve
```

## Teknologi yang Digunakan
- Bahasa Pemrograman: PHP 8.2
- Framework: Laravel 12
- Database: MySQL
- Frontend Library: TailwindCSS