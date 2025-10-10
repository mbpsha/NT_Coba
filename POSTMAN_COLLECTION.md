# 🛍️ E-COMMERCE API - COMPLETE POSTMAN TESTING GUIDE

## 📋 **CHECKLIST FITUR**

### 👤 **USER FEATURES:**

-   ✅ Login/Logout
-   ✅ Lihat Produk
-   ✅ Lihat Berita
-   ✅ Fitur Keranjang
-   ✅ Checkout Barang
-   ✅ Menambahkan Ulasan
-   ✅ Menambahkan Alamat Baru di Halaman Checkout
-   ✅ Melihat Update Barang yang Dibeli

### 👨‍💼 **ADMIN FEATURES:**

-   ✅ Login/Logout
-   ✅ Upload Produk
-   ✅ Verifikasi Pembayaran
-   ✅ Update Status Produk
-   ✅ Update Data Penjualan (Grafik)
-   ✅ Update Jumlah Transaksi Hari Ini
-   ✅ Riwayat Transaksi Hari Ini
-   ✅ Update Stok & Terjual

---

## 🚀 **POSTMAN COLLECTION**

### **Base URL:** `http://127.0.0.1:8000/api`

---

## 🔐 **1. AUTHENTICATION**

### **1.1 Register User**

```
POST {{base_url}}/register
Content-Type: application/json

{
    "nama": "Test User",
    "username": "testuser",
    "email": "test@example.com",
    "password": "password123",
    "no_telp": "081234567890",
    "alamat": "Jl. Test No. 123",
    "role": "user"
}
```

### **1.2 Register Admin**

```
POST {{base_url}}/register
Content-Type: application/json

{
    "nama": "Admin User",
    "username": "admin",
    "email": "admin@example.com",
    "password": "admin123",
    "no_telp": "081234567891",
    "alamat": "Jl. Admin No. 456",
    "role": "admin"
}
```

### **1.3 Login (Email/Username)**

```
POST {{base_url}}/login
Content-Type: application/json

{
    "login": "testuser",
    "password": "password123"
}

// OR with email
{
    "login": "test@example.com",
    "password": "password123"
}
```

### **1.4 Get User Info**

```
GET {{base_url}}/user
Authorization: Bearer {{token}}
```

### **1.5 Logout**

```
POST {{base_url}}/logout
Authorization: Bearer {{token}}
```

---

## 📦 **2. USER FEATURES**

### **2.1 Lihat Produk**

#### **Get All Products (Public)**

```
GET {{base_url}}/products
```

#### **Get Product Detail (Public)**

```
GET {{base_url}}/products/1
```

### **2.2 Lihat Berita**

#### **Get All Blogs (Public)**

```
GET {{base_url}}/blogs
```

#### **Get Blog Detail (Public)**

```
GET {{base_url}}/blogs/1
```

### **2.3 Fitur Keranjang**

#### **Create Cart**

```
POST {{base_url}}/carts
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "id_user": 1
}
```

#### **Add Item to Cart**

```
POST {{base_url}}/cart-details
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "id_keranjang": 1,
    "id_produk": 1,
    "jumlah": 2,
    "harga_satuan": 2500000
}
```

#### **Get Cart Items**

```
GET {{base_url}}/carts/1/items
Authorization: Bearer {{token}}
```

#### **Update Cart Item Quantity**

```
PATCH {{base_url}}/cart-details/1/quantity
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "jumlah": 5
}
```

#### **Remove Item from Cart**

```
DELETE {{base_url}}/cart-details/1
Authorization: Bearer {{token}}
```

### **2.4 Menambahkan Alamat Baru**

#### **Add New Address**

```
POST {{base_url}}/addresses
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "id_user": 1,
    "nama_penerima": "John Doe",
    "alamat": "Jl. Sudirman No. 123",
    "kota": "Jakarta",
    "kode_pos": "12345",
    "no_telp": "081234567890"
}
```

#### **Get User Addresses**

```
GET {{base_url}}/users/1/addresses
Authorization: Bearer {{token}}
```

### **2.5 Checkout Barang**

#### **Process Checkout**

```
POST {{base_url}}/checkout
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "id_user": 1,
    "id_keranjang": 1,
    "id_alamat": 1,
    "metode_pembayaran": "transfer",
    "total_pembayaran": 100000
}
```

#### **Track Order**

```
GET {{base_url}}/orders/1/tracking
Authorization: Bearer {{token}}
```

### **2.6 Menambahkan Ulasan**

#### **Add Product Review**

```
POST {{base_url}}/reviews
Authorization: Bearer {{token}}
Content-Type: application/json

{
    "id_user": 1,
    "id_produk": 1,
    "rating": 5,
    "komentar": "Produk sangat bagus! Recommended!"
}
```

#### **Get Product Reviews**

```
GET {{base_url}}/reviews?id_produk=1
```

### **2.7 Melihat Update Barang yang Dibeli**

#### **Get User Orders**

```
GET {{base_url}}/orders
Authorization: Bearer {{token}}
```

#### **Get Order Detail**

```
GET {{base_url}}/orders/1
Authorization: Bearer {{token}}
```

---

## 👨‍💼 **3. ADMIN FEATURES**

### **3.1 Upload Produk**

#### **Create Product**

```
POST {{base_url}}/products
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "nama": "Smartphone XYZ",
    "deskripsi": "Smartphone terbaru dengan fitur canggih",
    "harga": 2500000,
    "kategori": "Elektronik",
    "stok": 50,
    "gambar": "smartphone_xyz.jpg"
}
```

#### **Update Product**

```
PUT {{base_url}}/products/1
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "nama": "Smartphone XYZ Pro",
    "deskripsi": "Smartphone terbaru dengan fitur canggih dan kamera pro",
    "harga": 3000000,
    "kategori": "Elektronik",
    "stok": 30,
    "gambar": "smartphone_xyz_pro.jpg"
}
```

### **3.2 Verifikasi Pembayaran**

#### **Verify Payment**

```
PATCH {{base_url}}/payments/1/verify
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "status": "verified"
}
```

### **3.3 Update Status Produk (Order)**

#### **Update Order Status**

```
PATCH {{base_url}}/orders/1/status
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "status": "shipped"
}
```

### **3.4 Dashboard Analytics**

#### **Get Statistics**

```
GET {{base_url}}/dashboard/statistics
Authorization: Bearer {{admin_token}}
```

#### **Get Sales Data**

```
GET {{base_url}}/dashboard/sales-data
Authorization: Bearer {{admin_token}}
```

#### **Get Product Stats**

```
GET {{base_url}}/dashboard/product-stats
Authorization: Bearer {{admin_token}}
```

#### **Get Today Transactions**

```
GET {{base_url}}/dashboard/today-transactions
Authorization: Bearer {{admin_token}}
```

### **3.5 Manage Content**

#### **Create Blog/News**

```
POST {{base_url}}/blogs
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "judul": "Promo Akhir Tahun 2025",
    "konten": "Dapatkan diskon hingga 50% untuk semua produk elektronik",
    "id_penulis": 2,
    "gambar": "promo_2025.jpg"
}
```

---

## 🧪 **TESTING WORKFLOW**

### **Step 1: Setup Users**

1. Register user biasa
2. Register admin
3. Login dan simpan token

### **Step 2: Test User Flow**

1. Login user → Lihat produk → Add to cart → Checkout → Add review → Track order

### **Step 3: Test Admin Flow**

1. Login admin → Upload product → Verify payments → Update order status → Check dashboard

### **Step 4: Integration Test**

1. User beli produk → Admin verify payment → Admin update status → User track order

---

## 📝 **POSTMAN ENVIRONMENT VARIABLES**

```json
{
    "base_url": "http://127.0.0.1:8000/api",
    "token": "{{your_user_token}}",
    "admin_token": "{{your_admin_token}}"
}
```

---

## ⚠️ **IMPORTANT NOTES**

1. **Authentication Required**: Endpoints dengan 🔒 memerlukan Bearer token
2. **Admin Only**: Dashboard endpoints hanya untuk admin
3. **Public Access**: Products dan Blogs bisa diakses tanpa auth
4. **IDs**: Ganti ID sesuai data yang ada di database
5. **Tokens**: Update token setelah login

---

## 🚨 **ERROR CODES**

-   **200**: Success
-   **201**: Created
-   **401**: Unauthorized (Invalid/Missing token)
-   **403**: Forbidden (Insufficient permissions)
-   **404**: Not Found
-   **422**: Validation Error
-   **500**: Server Error
