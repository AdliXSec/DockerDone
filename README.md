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
