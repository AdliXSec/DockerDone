<<<<<<< HEAD
# MedTech Microservices System

Aplikasi ini adalah simulasi sistem manajemen pemesanan (e-commerce) kesehatan berbasis Microservices menggunakan arsitektur Event-Driven dengan RabbitMQ.

## 🏗 Arsitektur Sistem

Sistem ini telah dimodifikasi menjadi arsitektur Microservice yang sepenuhnya terisolasi, di mana setiap service memiliki file `docker-compose.yml` dan databasenya sendiri.

1. **RabbitMQ (Shared Infra - Port 5672 & 15672):** Message Broker pusat untuk komunikasi asinkron antar service.
2. **UI Service (Laravel - Port 8000):** Menangani antarmuka pengguna (Frontend).
3. **Order Service (Laravel - Port 8001):** Mengelola transaksi pesanan.
4. **Product Service (Laravel - Port 8002):** Mengelola katalog dan stok produk.
5. **User Service (Python/Flask - Port 5001):** Mengelola autentikasi dan manajemen pengguna.

---

## 🚀 Panduan Instalasi (Untuk Developer Baru)

Ikuti langkah-langkah di bawah ini secara berurutan untuk menjalankan project ini di komputer Anda setelah melakukan _clone_.

### Prasyarat

Pastikan Anda sudah menginstal:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Git

### Langkah 1: Clone & Setup Environment

1. Clone repository ini:
   ```bash
   git clone <link repo gua>
   cd <nama repo gua>
   ```

2. Siapkan file konfigurasi (`.env`) untuk masing-masing service. Salin file `.env.example` menjadi `.env` di setiap folder:
   *(Gunakan terminal CMD/PowerShell di Windows)*
   ```cmd
   copy uiservice\.env.example uiservice\.env
   copy orderservice\.env.example orderservice\.env
   copy productservice\.env.example productservice\.env
   copy userservice\.env.example userservice\.env
   ```

### Langkah 2: Menjalankan Container (Per-Service)

Karena sistem ini menganut arsitektur Microservice murni, Anda harus menjalankan container secara berurutan dimulai dari infrastruktur utamanya (RabbitMQ).

Jalankan perintah ini satu per satu di terminal root project:

```bash
# 1. Jalankan RabbitMQ & Network
cd rabbitmq
docker compose up -d
cd ..

# 2. Jalankan User Service (Python)
cd userservice
docker compose up -d --build
cd ..

# 3. Jalankan Order Service (Laravel)
cd orderservice
docker compose up -d --build
cd ..

# 4. Jalankan Product Service (Laravel)
cd productservice
docker compose up -d --build
cd ..

# 5. Jalankan UI Service (Laravel)
cd uiservice
docker compose up -d --build
cd ..
```

*(Catatan: Proses up pertama kali akan memakan waktu karena akan mem-build image lokal dan menginstall `vendor`/`dependencies` di dalam background).*

### Langkah 3: Setup Database & Kunci Enkripsi

Tunggu sekitar 1-2 menit setelah menjalankan perintah di atas agar container Laravel selesai menginstall Composer. Kemudian, jalankan perintah berikut untuk menginisialisasi database dan _Application Key_:

**1. Setup Flask (User Service):**
```bash
docker exec -it medtech-userservice python init_db.py
```

**2. Setup Laravel Services:**
```bash
# Generate Key
docker exec -it medtech-uiservice php artisan key:generate
docker exec -it medtech-orderservice php artisan key:generate
docker exec -it medtech-productservice php artisan key:generate

# Bersihkan Config Cache agar Key yang baru dibuat bisa langsung terbaca
docker exec -it medtech-uiservice php artisan config:clear
docker exec -it medtech-orderservice php artisan config:clear
docker exec -it medtech-productservice php artisan config:clear

# Migrasi Database
docker exec -it medtech-uiservice php artisan migrate --force
docker exec -it medtech-orderservice php artisan migrate --force
docker exec -it medtech-productservice php artisan migrate --force
```

---

## ⚙️ Menjalankan Sistem Asinkron (RabbitMQ Workers)

Agar data antar-service tersinkronisasi (misal: pendaftaran user masuk ke sistem Order, atau stok obat berkurang otomatis saat terjadi checkout), Anda **WAJIB** menjalankan Worker RabbitMQ.

Buka terminal baru untuk masing-masing perintah di bawah ini dan biarkan berjalan di _background_:

**Terminal 1: Menangkap Event User Baru (Order Service)**
```bash
cd orderservice
docker exec -it medtech-orderservice php artisan queue:work rabbitmq_users
```

**Terminal 2: Menangkap Event Potong Stok Produk (Product Service)**
```bash
cd productservice
docker exec -it medtech-productservice php artisan queue:work rabbitmq --queue=product_stock_queue
```

---

## 🌐 Akses Aplikasi

Setelah semuanya berjalan, Anda bisa mengakses layanan melalui browser:

- **Aplikasi Web Utama (UI Service):** [http://localhost:8000](http://localhost:8000)
- **Order Service API:** [http://localhost:8001](http://localhost:8001)
- **Product Service API:** [http://localhost:8002](http://localhost:8002)
- **User Service API:** [http://localhost:5001](http://localhost:5001)
- **RabbitMQ Management (Monitor Antrean):** [http://localhost:15672](http://localhost:15672) (User: `guest` | Pass: `guest`)

## 🛑 Cara Mematikan Aplikasi

Untuk mematikan sistem, Anda harus melakukan _down_ di masing-masing direktori:

```bash
cd uiservice && docker compose down && cd ..
cd productservice && docker compose down && cd ..
cd orderservice && docker compose down && cd ..
cd userservice && docker compose down && cd ..
cd rabbitmq && docker compose down && cd ..
```
=======
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
>>>>>>> 835cc29 (product commit ke1)
