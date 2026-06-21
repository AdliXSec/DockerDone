# MedTech Microservices System

Aplikasi ini adalah simulasi sistem manajemen pemesanan (e-commerce) kesehatan berbasis Microservices menggunakan arsitektur Event-Driven dengan RabbitMQ.

## 🏗 Arsitektur Sistem

Sistem ini menggunakan arsitektur Microservice yang sepenuhnya terisolasi, di mana setiap service memiliki file `docker-compose.yml` dan databasenya sendiri. Setiap service Laravel menggunakan pola **3-container** (PHP-FPM + Nginx + Database) sesuai standar deployment microservices.

1. **RabbitMQ (Shared Infra - Port 5672 & 15672):** Message Broker pusat untuk komunikasi asinkron antar service.
2. **UI Service (Laravel - Port 8000):** Menangani antarmuka pengguna (Frontend).
3. **Order Service (Laravel - Port 8001):** Mengelola transaksi pesanan.
4. **Product Service (Laravel - Port 8002):** Mengelola katalog dan stok produk.
5. **User Service (Python/Flask - Port 5001):** Mengelola autentikasi dan manajemen pengguna.

### Struktur Docker Container

```
project-root/
├── rabbitmq/
│   └── docker-compose.yml          # RabbitMQ + Network creator
├── userservice/
│   ├── docker-compose.yml          # Flask App + PostgreSQL (2 container)
│   └── Dockerfile
├── orderservice/
│   ├── docker-compose.yml          # PHP-FPM + Nginx + MySQL (3 container)
│   ├── Dockerfile
│   └── nginx/default.conf
├── productservice/
│   ├── docker-compose.yml          # PHP-FPM + Nginx + MySQL (3 container)
│   ├── Dockerfile
│   └── nginx/default.conf
├── uiservice/
│   ├── docker-compose.yml          # PHP-FPM + Nginx + MySQL (3 container)
│   ├── Dockerfile
│   └── nginx/default.conf
├── start-all.sh                    # Startup script (Linux/Mac)
├── start-all.ps1                   # Startup script (Windows)
├── stop-all.sh                     # Shutdown script (Linux/Mac)
└── stop-all.ps1                    # Shutdown script (Windows)
```

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
   git clone <link repo>
   cd <nama repo>
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

**Cara Cepat (Script Otomatis):**

```bash
# Linux/Mac
chmod +x start-all.sh
./start-all.sh

# Windows (PowerShell)
powershell -ExecutionPolicy Bypass -File .\start-all.ps1
```

---

## 🌐 Akses Aplikasi

Setelah semuanya berjalan, Anda bisa mengakses layanan melalui browser:

- **Aplikasi Web Utama (UI Service):** [http://localhost:8000](http://localhost:8000)
- **Order Service API:** [http://localhost:8001](http://localhost:8001)
- **Product Service API:** [http://localhost:8002](http://localhost:8002)
- **User Service API:** [http://localhost:5001](http://localhost:5001)
- **RabbitMQ Management (Monitor Antrean):** [http://localhost:15672](http://localhost:15672) (User: `guest` | Pass: `guest`)

### Daftar Container yang Berjalan

| Container Name | Image | Port | Fungsi |
|---------------|-------|------|--------|
| `medtech-rabbitmq` | rabbitmq:3-management | 5672, 15672 | Message Broker |
| `medtech-userservice` | custom (Python Flask) | 5001 | User Service App |
| `medtech-userservice-db` | postgres:16-alpine | 5432 | User Service Database (PostgreSQL) |
| `medtech-orderservice` | custom (PHP-FPM) | - | Order Service App (REST + GraphQL) |
| `medtech-orderservice-nginx` | nginx:stable-alpine | 8001 | Order Service Web Server |
| `medtech-orderservice-db` | mysql:8.0 | 3308 | Order Service Database |
| `medtech-productservice` | custom (PHP-FPM) | - | Product Service App (REST + GraphQL) |
| `medtech-productservice-nginx` | nginx:stable-alpine | 8002 | Product Service Web Server |
| `medtech-productservice-db` | mysql:8.0 | 3309 | Product Service Database |
| `medtech-uiservice` | custom (PHP-FPM) | - | UI Service App |
| `medtech-uiservice-nginx` | nginx:stable-alpine | 8000 | UI Service Web Server |
| `medtech-uiservice-db` | mysql:8.0 | 3310 | UI Service Database |

**Total: 12 container terpisah** (4 app + 3 nginx + 4 database + 1 message broker)

## 🛑 Cara Mematikan Aplikasi

**Cara Cepat (Script Otomatis):**

```bash
# Linux/Mac
./stop-all.sh

# Windows (PowerShell)
powershell -ExecutionPolicy Bypass -File .\stop-all.ps1
```

**Cara Manual:**

```bash
cd uiservice && docker compose down && cd ..
cd productservice && docker compose down && cd ..
cd orderservice && docker compose down && cd ..
cd userservice && docker compose down && cd ..
cd rabbitmq && docker compose down && cd ..
```
