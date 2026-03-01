````md
# 🚀 Laravel Clean Architecture + CQRS Project

![MIT License](https://img.shields.io/badge/License-MIT-green.svg)
![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel&logoColor=white)
![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)

Aplikasi berbasis **Laravel 12** dengan pendekatan  
**Clean Architecture + CQRS (Command & Query Responsibility Segregation)**.

Dirancang untuk:
- 🌐 REST API (JSON)
- 🖥 Web Interface (Blade)
- 🔐 API Authentication (Laravel Sanctum)
- 🗄 Multi-database ready (MySQL, PostgreSQL, SQL Server, Oracle)
- 📈 Struktur scalable & maintainable

---

## 📚 Table of Contents

- [✨ Spesifikasi](#-spesifikasi)
- [🏗 Arsitektur](#-arsitektur)
- [🧠 Clean Architecture & CQRS](#-clean-architecture--cqrs)
- [🚌 CommandBus & QueryBus](#-commandbus--querybus)
- [🔄 Flow CQRS](#-flow-cqrs)
- [📂 Struktur Folder](#-struktur-folder)
- [⚙ Instalasi](#-instalasi)
- [🔐 Authentication (Sanctum)](#-authentication-sanctum)
- [🌐 API Documentation](#-api-documentation)
- [📦 Pagination Helper](#-pagination-helper)
- [🔎 Search Case-Insensitive](#-search-case-insensitive)
- [🗄 Multi Database Setup](#-multi-database-setup)
- [🧪 Testing](#-testing)
- [🆕 Membuat Fitur Baru](#-membuat-fitur-baru)
- [🌱 Seeding](#-seeding)
- [🛠 Troubleshooting](#-troubleshooting)
- [🧩 Konvensi Coding](#-konvensi-coding)
- [📄 License](#-license)

---

## ✨ Spesifikasi

| Component        | Version |
|------------------|---------|
| 🐘 PHP           | 8.2+    |
| 🔥 Laravel       | 12.x    |
| 🔐 Sanctum       | API Token Authentication |
| 🗄 Database      | MySQL / PostgreSQL / SQL Server / Oracle |
| 🏗 Architecture  | Clean Architecture + CQRS |
| 🌐 Interface     | REST API + Blade |

---

## 🏗 Arsitektur

Struktur arsitektur mengikuti prinsip pemisahan tanggung jawab:

```text
Presentation → Application → Domain
        ↓
   Infrastructure
````

### 📌 Domain

* Entity
* Repository Contract
* Business Rules

### 📌 Application

* Command (Write Side)
* Query (Read Side)
* CommandBus & QueryBus

### 📌 Infrastructure

* Eloquent Model
* Repository Implementation
* Database Connection

### 📌 Presentation

* API Controller
* Web Controller
* Form Request
* Blade View

---

## 🧠 Clean Architecture & CQRS

### 🏛 Clean Architecture

Diperkenalkan oleh **Robert C. Martin (Uncle Bob)**.

Tujuan:

* Memisahkan business logic dari framework
* Meningkatkan testability
* Mengurangi ketergantungan pada database & UI
* Membuat sistem lebih scalable

#### 🔁 Dependency Rule

> Dependensi hanya boleh mengarah ke dalam (ke Domain).

```text
Framework / DB / UI
        ↓
Infrastructure
        ↓
Application
        ↓
Domain (Core Business)
```

Domain:

* Tidak tahu Laravel
* Tidak tahu HTTP
* Tidak tahu database

---

### ⚡ CQRS

Diperkenalkan oleh **Greg Young**.

Memisahkan:

* ✍ Command → Mengubah state
* 📖 Query → Mengambil data

Tujuan:

* Optimasi read & write
* Mengurangi kompleksitas
* Memudahkan scaling

---

## 🚌 CommandBus & QueryBus

Untuk menghindari tight coupling antara Controller dan Handler, digunakan mediator pattern:

* 🚌 CommandBus
* 🚌 QueryBus

### 🚌 CommandBus

Menangani operasi yang **mengubah state**.

Flow:

```text
Controller → Command → CommandBus → Handler → Repository → Database
```

Contoh:

```php
$command = new CreateUserCommand($name, $email, $password);
$this->commandBus->dispatch($command);
```

Karakteristik:

* Fokus pada perubahan data
* Tidak untuk mengambil data kompleks
* 1 Command = 1 Handler

---

### 🚌 QueryBus

Menangani operasi **read-only**.

Flow:

```text
Controller → Query → QueryBus → Handler → Read Model → Response
```

Contoh:

```php
$query = new ListUsersQuery($search, $page, $perPage);
$result = $this->queryBus->ask($query);
```

Karakteristik:

* Tidak mengubah state
* Return DTO / array / pagination
* Terpisah dari write logic

---

## 🔄 Flow CQRS

### ✍ Write Flow

1. Controller menerima request
2. Validasi via FormRequest
3. Membuat Command
4. Dispatch ke CommandBus
5. Handler memanggil Repository
6. Simpan ke database

### 📖 Read Flow

1. Controller menerima request
2. Membuat Query
3. QueryBus ask ke Handler
4. Handler ambil data
5. Return data + meta pagination

---

## 📂 Struktur Folder

```text
app/
├── Domain/
├── Application/
├── Infrastructure/
├── Presentation/
├── Supports/
routes/
```

---

## ⚙ Instalasi

---

# 🚀 Opsi 1 — Install sebagai Project Template (Recommended)

Gunakan jika ingin membuat project baru dari template Clean Architecture + CQRS.

```bash
composer create-project adityapratamaf/laravel-clean-architecture-cqrs my-app
cd my-app
php artisan serve
```

Akses:

- 🌐 http://127.0.0.1:8000
- 🔗 http://127.0.0.1:8000/api/users

---

# 📦 Opsi 2 — Install sebagai Package ke Project Laravel yang Sudah Ada

Gunakan jika ingin menambahkan Clean CQRS ke project Laravel existing.

### 1️⃣ Install Package

```bash
composer require adityapratamaf/clean-cqrs
```

### 2️⃣ Publish Configuration (jika ada)

```bash
php artisan vendor:publish --tag=clean-cqrs
```

### 3️⃣ Jalankan Installer

```bash
php artisan clean-cqrs:install
```

---

# 🛠 Manual Setup (Jika Clone Repository)

Jika kamu clone repository secara manual:

### 1️⃣ Install Dependencies

```bash
composer install
```

### 2️⃣ Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3️⃣ Konfigurasi Database di `.env`

Contoh PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_project
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

### 4️⃣ Migrasi Database

```bash
php artisan migrate
```

### 5️⃣ (Optional) Seeding

```bash
php artisan migrate:fresh --seed
```

### 6️⃣ Jalankan Server

```bash
php artisan serve
```

Akses:

- 🌐 http://127.0.0.1:8000
- 🔗 http://127.0.0.1:8000/api/users

---

## 🔐 Authentication (Sanctum)

Project menggunakan **Laravel Sanctum (Bearer Token)**.

### Install

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Middleware

```php
Route::middleware('auth:sanctum')->get('/me', MeController::class);
```

---

## 🌐 API Documentation

> Dokumentasi lengkap: **`api-documentation.md`**
> (Berisi contoh request/response & cURL untuk Login, Me, Logout, dan CRUD Users)

---

## 📦 Pagination Helper

📁 File: `app/Supports/Paginator.php`

```php
return Paginator::paginateWithMeta($query, $perPage, $page);
```

---

## 🔎 Search Case-Insensitive

```php
$qb->whereRaw('LOWER(name) LIKE ?', ["%{$s}%"]);
```

Compatible:

* MySQL
* PostgreSQL
* SQL Server
* Oracle

---

## 🗄 Multi Database Setup

`.env`:

```env
DB_CONNECTION=mysql
# DB_CONNECTION=pgsql
# DB_CONNECTION=sqlsrv
# DB_CONNECTION=oracle
```

Untuk Oracle:

```bash
composer require yajra/laravel-oci8
```

---

## 🧪 Testing

Project mendukung **Unit Test** dan **Feature Test** menggunakan PHPUnit (bawaan Laravel).

### ▶️ Menjalankan semua test

```bash
php artisan test
```

### 🧪 Menjalankan hanya Unit Test

```bash
php artisan test --testsuite=Unit
```

### 🧪 Menjalankan hanya Feature Test

```bash
php artisan test --testsuite=Feature
```

### 🎯 Menjalankan test tertentu

```bash
php artisan test --filter=UserCrudTest
```

```bash
php artisan test --filter=RegisterUserCommandHandlerTest
```

```bash
php artisan test --filter=LoginTest
```

### 🗄 Testing dengan database

Disarankan menggunakan database khusus testing atau SQLite in-memory.

Contoh `.env.testing`:

```env
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

Lalu jalankan:

```bash
php artisan test
```

> Tips: gunakan trait `RefreshDatabase` pada Feature Test agar migrasi otomatis dan database bersih tiap test.

---

## 🆕 Membuat Fitur Baru

1. Domain → Entity + Repository Contract
2. Infrastructure → Model + Repository Implementation
3. Application → Command & Query + Handler
4. Presentation → Controller

---

## 🌱 Seeding

```php
User::factory()->count(100)->create();
```

---

## 🛠 Troubleshooting

Route tidak muncul:

```bash
php artisan optimize:clear
php artisan route:list
```

Class tidak ditemukan:

```bash
composer dump-autoload
```

---

## 🧩 Konvensi Coding

* 📖 Read side tidak return Entity
* ✍ Write side boleh return Entity
* 🗄 Repository di Infrastructure
* 📦 Pagination wajib pakai helper
* 🎯 Controller hanya orchestration

---

## 📄 License

MIT License © 2026
**Aditya Pratama Febriono**
This project is open-sourced software licensed under the MIT license.

```