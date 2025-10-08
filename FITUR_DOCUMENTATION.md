# DOKUMENTASI API ECOMMERCE - FITUR YANG DITAMBAHKAN

## 📋 COMMAND ARTISAN YANG TELAH DIJALANKAN

```bash
# Membuat model, migration, dan controller untuk Address
php artisan make:migration create_addresses_table
php artisan make:model Address
php artisan make:request AddressRequest
php artisan make:controller AddressController --resource

# Membuat controller dan request untuk CartDetail
php artisan make:controller CartDetailController --resource
php artisan make:request CartDetailRequest

# Membuat controller untuk Checkout
php artisan make:controller CheckoutController

# Membuat controller untuk Dashboard admin
php artisan make:controller DashboardController

# Membuat middleware untuk admin
php artisan make:middleware AdminMiddleware

# Menambah kolom id_alamat ke tabel orders
php artisan make:migration add_id_alamat_to_orders_table --table=orders

# Jalankan migration setelah semua siap
php artisan migrate
```

## 🚀 FITUR YANG TELAH DITAMBAHKAN

### ✅ FITUR PENGGUNA:

#### 1. **Manajemen Alamat**

-   **Endpoint**: `/api/addresses`
-   **Method**: GET, POST, PUT, DELETE
-   **Fitur**: Tambah, edit, hapus alamat pengiriman
-   **Fitur khusus**: Set alamat default
-   **Endpoint khusus**:
    -   `GET /api/users/{userId}/addresses` - Ambil alamat berdasarkan user

#### 2. **Manajemen Keranjang Belanja**

-   **Endpoint**: `/api/cart-details`
-   **Method**: GET, POST, PUT, DELETE
-   **Fitur**:
    -   Tambah produk ke keranjang
    -   Update quantity
    -   Hapus item dari keranjang
-   **Endpoint khusus**:
    -   `GET /api/carts/{cartId}/items` - Ambil semua item dalam keranjang
    -   `PATCH /api/cart-details/{id}/quantity` - Update quantity saja

#### 3. **Proses Checkout**

-   **Endpoint**: `/api/checkout`
-   **Method**: POST
-   **Fitur**:
    -   Validasi stok produk
    -   Kalkulasi total harga
    -   Buat order dan order detail
    -   Update stok produk
    -   Buat record pembayaran
    -   Kosongkan keranjang
-   **Endpoint tracking**:
    -   `GET /api/orders/{orderId}/tracking` - Tracking status pesanan

### ✅ FITUR ADMIN:

#### 1. **Dashboard Analytics**

-   **Endpoint**: `/api/dashboard/statistics`
-   **Method**: GET
-   **Data**: Total users, products, orders, revenue hari ini, dll

#### 2. **Data Penjualan**

-   **Endpoint**: `/api/dashboard/sales-data`
-   **Method**: GET
-   **Parameter**: `?days=7` (default 7 hari)
-   **Data**: Grafik penjualan per hari

#### 3. **Transaksi Hari Ini**

-   **Endpoint**: `/api/dashboard/today-transactions`
-   **Method**: GET
-   **Data**: Semua transaksi hari ini

#### 4. **Statistik Produk**

-   **Endpoint**: `/api/dashboard/product-stats`
-   **Method**: GET
-   **Data**: Produk dengan stok rendah

#### 5. **Verifikasi Pembayaran**

-   **Endpoint**: `/api/payments/{paymentId}/verify`
-   **Method**: PATCH
-   **Body**: `{"status": "berhasil|gagal", "bukti_pembayaran": "url"}`

#### 6. **Update Status Order**

-   **Endpoint**: `/api/orders/{orderId}/status`
-   **Method**: PATCH
-   **Body**: `{"status": "pending|dikonfirmasi|diproses|dikirim|selesai|dibatalkan"}`

## 🔐 SISTEM AUTENTIKASI & OTORISASI

### Middleware yang ditambahkan:

-   **AdminMiddleware**: Memastikan hanya user dengan role 'admin' yang bisa akses fitur admin
-   **Route Protection**:
    -   Public: Lihat produk dan blog
    -   Auth Required: Fitur user (keranjang, checkout, alamat)
    -   Admin Required: CRUD produk/blog, dashboard, verifikasi

## 📊 STRUKTUR DATABASE YANG DITAMBAHKAN

### Tabel `addresses`:

```sql
- id_alamat (PK)
- id_user (FK)
- label (varchar)
- nama_penerima (varchar)
- no_telp_penerima (varchar)
- alamat_lengkap (text)
- kota (varchar)
- provinsi (varchar)
- kode_pos (varchar)
- is_default (boolean)
- created_at, updated_at
```

### Update Tabel `orders`:

```sql
+ id_alamat (FK) - Referensi ke alamat pengiriman
```

## 🔗 RELASI MODEL YANG DITAMBAHKAN

-   **User** hasMany **Address**
-   **Order** belongsTo **Address**
-   **CartDetail** belongsTo **Cart** dan **Product**
-   **OrderDetail** belongsTo **Order** dan **Product**

## 📝 CARA PENGGUNAAN

### 1. Jalankan Migration:

```bash
php artisan migrate
```

### 2. Seed Data Admin (opsional):

```bash
# Buat user admin melalui seeder atau manual di database
# Set role = 'admin' untuk user yang akan jadi admin
```

### 3. Test Endpoint:

```bash
# Login sebagai user biasa
POST /api/login

# Tambah alamat
POST /api/addresses
{
  "id_user": 1,
  "label": "Rumah",
  "nama_penerima": "John Doe",
  "no_telp_penerima": "08123456789",
  "alamat_lengkap": "Jl. Contoh No. 123",
  "kota": "Jakarta",
  "provinsi": "DKI Jakarta",
  "kode_pos": "12345",
  "is_default": true
}

# Tambah produk ke keranjang
POST /api/cart-details
{
  "id_keranjang": 1,
  "id_produk": 1,
  "jumlah": 2,
  "harga_satuan": 50000
}

# Checkout
POST /api/checkout
{
  "id_user": 1,
  "id_alamat": 1,
  "metode_pembayaran": "transfer_bank"
}
```

## 🎯 FITUR LENGKAP YANG SUDAH TERSEDIA

### Pengguna:

-   ✅ Login/Logout
-   ✅ Lihat Produk
-   ✅ Lihat Berita
-   ✅ Fitur Keranjang (lengkap)
-   ✅ Checkout Barang (dengan validasi stok)
-   ✅ Menambahkan Ulasan
-   ✅ Menambahkan Alamat (di halaman checkout)
-   ✅ Melihat Update Barang yang Dibeli (tracking)

### Admin:

-   ✅ Login/Logout
-   ✅ Upload Produk
-   ✅ Verifikasi Pembayaran
-   ✅ Update Status Produk
-   ✅ Update Data Penjualan (grafik)
-   ✅ Update Jumlah Transaksi Hari Ini
-   ✅ Riwayat Transaksi Hari Ini
-   ✅ Update Stok & Terjual (otomatis saat checkout)

## 🚨 CATATAN PENTING

1. **Migration**: Pastikan menjalankan `php artisan migrate` untuk tabel baru
2. **Seeder**: Buat data admin dengan role 'admin' di tabel users
3. **Storage**: Untuk upload gambar, pastikan storage link sudah dibuat
4. **Testing**: Test semua endpoint dengan Postman atau tools API lainnya
5. **Security**: Semua route admin sudah diproteksi dengan middleware admin

Semua fitur sudah lengkap dan siap digunakan! 🎉
