# 📋 Laporan Analisis Tugas Besar - Integrasi Aplikasi Enterprise

**Mata Kuliah:** Integrasi Aplikasi Enterprise (IAE)  
**Judul Project:** Pengembangan Studi Kasus Sistem Terintegrasi Berbasis Microservices  
**Nama Project:** MedTech Microservices System (E-Commerce Obat)

---

## 📌 Ringkasan Sistem

Sistem ini merupakan simulasi platform e-commerce obat (MedTech) yang dibangun menggunakan arsitektur **Microservices** dengan komunikasi **Event-Driven** via **RabbitMQ**. Sistem terdiri dari 4 service utama yang masing-masing berjalan dalam **container Docker terpisah** dengan **database terpisah**.

### Arsitektur Service

| Service | Framework | Port | Database | Deskripsi |
|---------|-----------|------|----------|-----------|
| **UserService** | Python (Flask) | 5001 | `users_db` (MySQL) | Autentikasi & manajemen pengguna |
| **OrderService** | PHP (Laravel) | 8001 | `orders_db` (MySQL) | Manajemen transaksi/pesanan |
| **ProductService** | PHP (Laravel) | 8002 | `products_db` (MySQL) | Manajemen katalog & stok obat |
| **UIService** | PHP (Laravel) | 8000 | `ui_db` (MySQL) | Antarmuka pengguna (Frontend) |

### Infrastruktur Pendukung

| Komponen | Image | Port | Fungsi |
|----------|-------|------|--------|
| **RabbitMQ** | `rabbitmq:3-management` | 5672, 15672 | Message Broker (Event-Driven Communication) |
| **MySQL x4** | `mysql:8.0` | 3307-3310 | Database terpisah per service |

---

## 🔍 Analisis Per-Aspek Penilaian

---

### 1. GraphQL Implementation (Skor Maksimal: 20)

#### Status: ❌ BELUM DIIMPLEMENTASIKAN

Saat ini, **belum ada implementasi GraphQL sama sekali** dalam project ini. Semua komunikasi data menggunakan REST API.

#### Yang Perlu Diimplementasikan untuk Nilai Sempurna:

**a) GraphQL Backend Manual (Framework-based)**
- Mengimplementasikan GraphQL endpoint di salah satu service menggunakan library:
  - Untuk Laravel: package `rebing/graphql-laravel` atau `nuwave/lighthouse`
  - Untuk Flask: library `graphene` atau `ariadne`
- Membuat **schema GraphQL** yang mendefinisikan:
  - Types (User, Obat/Product, Order)
  - Queries (getUsers, getObat, getOrders, dll)
  - Mutations (createOrder, updateObat, dll)
- Contoh query yang harus bisa dijalankan:
  ```graphql
  query {
    users {
      id
      name
      email
      role
    }
  }

  query {
    obat(id: 1) {
      name
      category
      price
      stock
    }
  }

  mutation {
    createOrder(input: {
      product_id: 1
      user_id: 1
      quantity: 5
    }) {
      order_code
      total_price
      status
    }
  }
  ```

**b) Hasura GraphQL Engine**
- Menambahkan container Hasura di `docker-compose.infra.yml` atau compose terpisah
- Menghubungkan Hasura ke salah satu/beberapa database MySQL
- Hasura secara otomatis menghasilkan GraphQL API dari tabel database
- Setup Hasura meliputi:
  - Container `hasura/graphql-engine`
  - Console akses di port `8080`
  - Tracking tabel dan relasi
  - Permissions per-role

#### Rekomendasi Implementasi:
1. Tambahkan GraphQL manual di **ProductService** (Laravel + `rebing/graphql-laravel`)
2. Tambahkan container **Hasura** yang terhubung ke `orders_db` untuk demonstrasi auto-generated GraphQL
3. Dokumentasikan schema dan contoh query di Postman atau GraphQL Playground

---

### 2. Docker Deployment (Skor Maksimal: 20)

#### Status: ✅ SUDAH MEMENUHI (Setelah Pemisahan)

Setelah pemisahan docker-compose yang telah dilakukan, sekarang setup sudah memenuhi kriteria **Memenuhi (100%)**:

#### Yang Sudah Terpenuhi:
- ✅ Setiap service berjalan di **container terpisah** (`medtech-userservice`, `medtech-orderservice`, `medtech-productservice`, `medtech-uiservice`)
- ✅ Setiap service memiliki **database MySQL terpisah** di container masing-masing (`medtech-userservice-db`, `medtech-orderservice-db`, `medtech-productservice-db`, `medtech-uiservice-db`)
- ✅ **Shared network** (`medtech-network`) memungkinkan komunikasi antar service
- ✅ Setup bisa dijalankan dengan perintah yang jelas dan terstruktur
- ✅ **Healthcheck** diimplementasikan pada semua database container
- ✅ **Volume** untuk persistensi data

#### Cara Menjalankan:

```bash
# Langkah 1: Jalankan infrastruktur bersama (RabbitMQ + Network)
docker compose -f docker-compose.infra.yml up -d

# Langkah 2: Jalankan setiap service secara terpisah
cd userservice && docker compose up -d --build && cd ..
cd orderservice && docker compose up -d --build && cd ..
cd productservice && docker compose up -d --build && cd ..
cd uiservice && docker compose up -d --build && cd ..
```

#### Struktur Docker Compose:

```
project-root/
├── docker-compose.infra.yml        # Shared: RabbitMQ + Network
├── userservice/
│   ├── docker-compose.yml          # UserService + MySQL
│   └── Dockerfile
├── orderservice/
│   ├── docker-compose.yml          # OrderService + MySQL
│   └── Dockerfile
├── productservice/
│   ├── docker-compose.yml          # ProductService + MySQL
│   └── Dockerfile
└── uiservice/
    ├── docker-compose.yml          # UIService + MySQL
    └── Dockerfile
```

#### Daftar Container yang Berjalan:

| Container Name | Image | Port |
|---------------|-------|------|
| `medtech-rabbitmq` | rabbitmq:3-management | 5672, 15672 |
| `medtech-userservice` | custom (Python Flask) | 5001 |
| `medtech-userservice-db` | mysql:8.0 | 3307 |
| `medtech-orderservice` | custom (PHP Laravel) | 8001 |
| `medtech-orderservice-db` | mysql:8.0 | 3308 |
| `medtech-productservice` | custom (PHP Laravel) | 8002 |
| `medtech-productservice-db` | mysql:8.0 | 3309 |
| `medtech-uiservice` | custom (PHP Laravel) | 8000 |
| `medtech-uiservice-db` | mysql:8.0 | 3310 |

**Total: 9 container terpisah** (4 service + 4 database + 1 message broker)

---

### 3. RESTful dan Message Broker Implementation (Skor Maksimal: 25)

#### Status: ✅ SUDAH MEMENUHI (100%)

Kedua komponen RESTful API dan Message Broker sudah diimplementasikan dengan baik.

#### a) RESTful API

**UserService (Flask) — REST Endpoints:**

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| `POST` | `/register` | Registrasi user baru | ❌ |
| `POST` | `/login` | Login (mendapat JWT token) | ❌ |
| `GET` | `/refresh` | Refresh token | ✅ JWT |
| `POST` | `/logout` | Logout (revoke token) | ✅ JWT |
| `GET` | `/users` | Daftar semua user | ❌ |
| `GET` | `/users/{id}` | Detail user | ❌ |
| `PUT` | `/users/{id}` | Update user | ✅ JWT |
| `DELETE` | `/users/{id}` | Hapus user | ✅ JWT |
| `GET` | `/is_login` | Cek status login | ✅ JWT |

**OrderService (Laravel) — REST Endpoints:**

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| `GET` | `/api/orders` | Daftar semua order | ✅ JWT + Admin |
| `GET` | `/api/orders/{id}` | Detail order | ✅ JWT |
| `GET` | `/api/orders/user/{id}` | Order per user | ✅ JWT |
| `POST` | `/api/orders` | Buat order baru | ✅ JWT |
| `PUT` | `/api/orders/{id}` | Update order | ✅ JWT + Admin |
| `PATCH` | `/api/orders/{id}/status` | Update status order | ✅ JWT + Admin |
| `DELETE` | `/api/orders/{id}` | Hapus order | ✅ JWT + Admin |

**ProductService (Laravel) — REST Endpoints:**

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| `GET` | `/api/obat` | Daftar semua obat | ✅ JWT |
| `GET` | `/api/obat/{id}` | Detail obat | ✅ JWT |
| `POST` | `/api/obat` | Tambah obat | ✅ JWT + Admin |
| `PUT` | `/api/obat/{id}` | Update obat | ✅ JWT + Admin |
| `DELETE` | `/api/obat/{id}` | Hapus obat | ✅ JWT + Admin |
| `PATCH` | `/api/obat/{id}/stock` | Update stok | ✅ JWT |

#### b) Message Broker (RabbitMQ)

**Event-Driven Communication yang Sudah Diimplementasikan:**

1. **Event: `user.registered`**
   - **Producer:** UserService (Flask) → mengirim event saat user baru mendaftar
   - **Consumer:** OrderService (Laravel) → menerima event dan menyimpan data user ke database lokal (`HandleUserRegistered` Job)
   - **Queue:** `user_events`
   - **Tujuan:** Sinkronisasi data user antar service

2. **Event: Product Stock Update**
   - **Producer:** OrderService (Laravel) → dispatch job saat order dibuat/diupdate/dihapus
   - **Consumer:** ProductService (Laravel) → menerima job dan mengupdate stok obat (`UpdateProductStock` Job)
   - **Queue:** `product_stock_queue`
   - **Tujuan:** Otomatis mengurangi/menambah stok obat berdasarkan transaksi

**Teknologi:**
- Message Broker: RabbitMQ 3 dengan Management UI
- Laravel Queue Driver: `vladimir-yuldashev/laravel-queue-rabbitmq`
- Python RabbitMQ Client: `pika`
- Management UI: `http://localhost:15672` (user: guest, pass: guest)

---

### 4. Dokumentasi & Arsitektur (Skor Maksimal: 15)

#### Status: ⚠️ PARSIAL — PERLU DILENGKAPI

#### Yang Sudah Ada:
- ✅ README.md dengan panduan instalasi
- ✅ Deskripsi service di README

#### Yang Masih KURANG untuk Nilai Sempurna:

**a) Diagram Arsitektur Sistem**
- ❌ Belum ada diagram visual arsitektur microservices
- Dibutuhkan:
  - Diagram integrasi antar service (service interaction diagram)
  - Diagram komunikasi (REST + RabbitMQ message flow)
  - Diagram deployment (Docker container topology)
- Tools yang bisa digunakan: Draw.io, Mermaid, PlantUML

**b) Dokumentasi API (Postman Collection)**
- ❌ Belum ada link Postman collection
- Dibutuhkan:
  - Export Postman collection yang mencakup semua endpoint
  - Environment variables untuk base URL dan token
  - Contoh request dan response untuk setiap endpoint
  - Link publik ke Postman collection

**c) Penjelasan Fitur & Flow**
- ⚠️ Kurang detail di dokumentasi saat ini
- Dibutuhkan:
  - Penjelasan setiap fitur utama (registrasi, login, CRUD obat, pemesanan)
  - Flow diagram bagaimana fitur berjalan melibatkan beberapa service
  - Contoh: "Saat user memesan obat: UIService → OrderService → RabbitMQ → ProductService (potong stok)"

**d) Link GitHub Repository**
- ⚠️ README menyebutkan 1 repo saja
- Idealnya: setiap service memiliki repo sendiri (sesuai prinsip microservices)
- Atau minimal: 1 monorepo dengan dokumentasi yang menjelaskan setiap service

#### Rekomendasi:
1. Buat diagram arsitektur menggunakan Mermaid atau Draw.io
2. Export Postman collection dan upload ke repo / Postman public workspace
3. Tulis penjelasan flow fitur utama secara detail
4. Pastikan link GitHub repo tersedia dan accessible

---

### 5. Presentasi & Demo (Skor Maksimal: 20)

#### Status: ⚠️ PERLU PERSIAPAN

#### Poin Demo yang Harus Disiapkan:

1. **Menjalankan Sistem**
   - Demo menjalankan semua container menggunakan Docker Compose terpisah
   - Menunjukkan semua 9 container berjalan (`docker ps`)

2. **Demo Fitur REST API**
   - Registrasi user baru (POST `/register`)
   - Login dan mendapat JWT token (POST `/login`)
   - CRUD Obat (GET, POST, PUT, DELETE `/api/obat`)
   - Buat order baru (POST `/api/orders`)
   - Tampilkan detail order dengan data user dan product (GET `/api/orders/{id}`)

3. **Demo Message Broker (RabbitMQ)**
   - Tunjukkan RabbitMQ Management UI (`http://localhost:15672`)
   - Demo sinkronisasi user: register di UserService → data muncul di OrderService
   - Demo potong stok: buat order → stok obat berkurang otomatis di ProductService
   - Tunjukkan log worker yang menunjukkan event diterima dan diproses

4. **Demo GraphQL** (Jika diimplementasikan)
   - Query data menggunakan GraphQL Playground
   - Bandingkan dengan REST API
   - Demo Hasura Console

5. **Penjelasan Arsitektur**
   - Jelaskan diagram arsitektur
   - Jelaskan teknologi yang digunakan per service
   - Jelaskan pattern komunikasi (synchronous REST vs asynchronous RabbitMQ)

#### Tips Presentasi:
- Siapkan semua container running sebelum demo
- Seed data (user, obat) terlebih dahulu agar demo lebih lancar
- Gunakan Postman untuk demo API (lebih visual)
- Siapkan terminal untuk menunjukkan docker logs

---

## 📊 Rangkuman Status Kesiapan

| No | Aspek | Skor Maks | Status | Estimasi Skor | Keterangan |
|----|-------|-----------|--------|---------------|------------|
| 1 | GraphQL Implementation | 20 | ❌ Belum ada | **0** | Perlu implementasi manual + Hasura |
| 2 | Docker Deployment | 20 | ✅ Memenuhi | **20** | Container terpisah per service |
| 3 | RESTful & Message Broker | 25 | ✅ Memenuhi | **25** | REST API + RabbitMQ sudah lengkap |
| 4 | Dokumentasi & Arsitektur | 15 | ⚠️ Parsial | **~7-10** | Perlu diagram, Postman, penjelasan flow |
| 5 | Presentasi & Demo | 20 | ⚠️ Persiapan | **~14-20** | Tergantung persiapan demo |
| | **TOTAL** | **100** | | **~46-75** | |

---

## 🚀 Prioritas Aksi untuk Nilai Sempurna

### 🔴 Prioritas Tinggi (Dampak Besar)
1. **Implementasi GraphQL** (20 poin)
   - Install `rebing/graphql-laravel` di ProductService atau OrderService
   - Buat schema, queries, dan mutations
   - Setup Hasura container yang terhubung ke database
   - Dokumentasikan contoh query

### 🟡 Prioritas Sedang (Pelengkap Penting)
2. **Dokumentasi Arsitektur** (bagian dari 15 poin)
   - Buat diagram arsitektur sistem (Mermaid/Draw.io)
   - Export dan share Postman collection
   - Tulis penjelasan flow fitur utama

### 🟢 Prioritas Rendah (Sudah OK)
3. **Persiapan Demo** (20 poin)
   - Siapkan script demo
   - Seed data sample
   - Test run semua scenario

---

## 📐 Diagram Arsitektur (Referensi untuk Dokumentasi)

```
┌─────────────────────────────────────────────────────────────────┐
│                      MedTech Microservices                       │
│                                                                   │
│  ┌──────────────┐    REST API    ┌──────────────────┐            │
│  │  UIService    │──────────────▶│  UserService      │            │
│  │  (Laravel)    │               │  (Flask/Python)    │            │
│  │  Port: 8000   │               │  Port: 5001        │            │
│  │  DB: ui_db    │               │  DB: users_db      │            │
│  └──────┬───────┘               └────────┬──────────┘            │
│         │                                 │                       │
│         │ REST API                        │ RabbitMQ              │
│         │                                 │ (user.registered)     │
│         ▼                                 ▼                       │
│  ┌──────────────────┐           ┌──────────────────┐             │
│  │  ProductService   │◀─────────│  OrderService     │             │
│  │  (Laravel)        │ RabbitMQ │  (Laravel)         │             │
│  │  Port: 8002       │ (stock)  │  Port: 8001        │             │
│  │  DB: products_db  │          │  DB: orders_db     │             │
│  └──────────────────┘           └──────────────────┘             │
│                                                                   │
│  ┌──────────────────────────────────────────────────┐            │
│  │  RabbitMQ (Message Broker)                        │            │
│  │  AMQP: 5672 | Management UI: 15672                │            │
│  │  Queues: user_events, product_stock_queue          │            │
│  └──────────────────────────────────────────────────┘            │
└─────────────────────────────────────────────────────────────────┘
```

### Flow Fitur Utama:

**1. Registrasi User:**
```
Client → UserService (POST /register) → Save to users_db
                                      → Publish "user.registered" to RabbitMQ
                                      → OrderService Worker consumes event
                                      → Sync user to orders_db
```

**2. Pemesanan Obat:**
```
Client → OrderService (POST /api/orders)
       → Fetch user data from UserService (REST)
       → Fetch product data from ProductService (REST)
       → Validate stock
       → Create order in orders_db
       → Dispatch UpdateProductStock job to RabbitMQ
       → ProductService Worker consumes job
       → Update stock in products_db
```

**3. Hapus Order:**
```
Client → OrderService (DELETE /api/orders/{id})
       → Delete order from orders_db
       → Dispatch UpdateProductStock (action: add) to RabbitMQ
       → ProductService Worker restores stock in products_db
```
