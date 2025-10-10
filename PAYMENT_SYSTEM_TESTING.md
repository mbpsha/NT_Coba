# 💳 **PAYMENT SYSTEM - POSTMAN TESTING GUIDE**

## 🎯 **SISTEM PEMBAYARAN SEPERTI GAMBAR - READY!**

### ✅ **YANG SUDAH DIUPDATE:**

1. **Migration**: Disederhanakan ke QRIS only
2. **PaymentController**: Ditambah 3 method baru
3. **Payment Model**: Updated dengan fields baru
4. **API Routes**: Ditambah 3 endpoint baru

---

## 🚀 **API ENDPOINTS UNTUK SISTEM PEMBAYARAN:**

### **1. Checkout & Create Payment**

```
POST {{base_url}}/checkout
Authorization: Bearer {{user_token}}
Content-Type: application/json

{
    "id_user": 1,
    "id_keranjang": 1,
    "id_alamat": 1,
    "metode_pembayaran": "qris",
    "total_pembayaran": 102500
}
```

**Response:**

```json
{
    "order": {
        "id_pemesanan": 1,
        "total_harga": 102500,
        "status_pesanan": "pending"
    },
    "payment": {
        "id_pembayaran": 1,
        "metode_pembayaran": "qris",
        "jumlah": 102500,
        "status_pembayaran": "pending"
    }
}
```

---

### **2. Get QR Code & Payment Details (Untuk Halaman Checkout)**

```
GET {{base_url}}/payments/1/qr-code
Authorization: Bearer {{user_token}}
```

**Response:**

```json
{
    "payment_details": {
        "id_pembayaran": 1,
        "metode_pembayaran": "qris",
        "jumlah": 102500,
        "status": "pending",
        "tanggal": "2 Jul 2025",
        "payment_method": "QRIS"
    },
    "qr_code_data": "eyJtZXJjaGFudCI6Ik5HVU5EVVIi...",
    "merchant_info": {
        "name": "NGUNDUR",
        "logo": "http://localhost:8000/images/logo.png"
    }
}
```

---

### **3. Upload Bukti Pembayaran**

```
POST {{base_url}}/payments/1/upload-proof
Authorization: Bearer {{user_token}}
Content-Type: multipart/form-data

Form Data:
bukti_pembayaran: [SELECT FILE - JPG/PNG/PDF, Max 5MB]
```

**Response:**

```json
{
    "message": "Bukti pembayaran berhasil diupload",
    "payment": {
        "id_pembayaran": 1,
        "bukti_pembayaran": "payment_proofs/payment_proof_1_1696745678.jpg",
        "status_pembayaran": "pending",
        "payment_date": "2025-07-02T10:30:00.000000Z"
    },
    "file_url": "http://localhost:8000/storage/payment_proofs/payment_proof_1_1696745678.jpg"
}
```

---

### **4. Check Payment Status**

```
GET {{base_url}}/payments/1/status
Authorization: Bearer {{user_token}}
```

**Response:**

```json
{
    "id_pembayaran": 1,
    "status": "pending",
    "jumlah": 102500,
    "metode_pembayaran": "qris",
    "bukti_pembayaran": "http://localhost:8000/storage/payment_proofs/payment_proof_1_1696745678.jpg",
    "payment_date": "2025-07-02T10:30:00.000000Z",
    "created_at": "2025-07-02T08:00:00.000000Z",
    "updated_at": "2025-07-02T10:30:00.000000Z"
}
```

---

### **5. Admin Verify Payment**

```
PATCH {{base_url}}/payments/1/verify
Authorization: Bearer {{admin_token}}
Content-Type: application/json

{
    "status": "verified"
}
```

---

## 🔄 **COMPLETE FLOW TESTING:**

### **Step 1: Persiapan**

1. Start server: `php artisan serve`
2. Login user → Get `user_token`
3. Login admin → Get `admin_token`

### **Step 2: User Flow (Seperti di Gambar)**

```bash
1. Create cart & add items
2. POST /checkout → Get payment_id
3. GET /payments/{id}/qr-code → Display QR & details
4. POST /payments/{id}/upload-proof → Upload bukti bayar
5. GET /payments/{id}/status → Check status
```

### **Step 3: Admin Flow**

```bash
1. GET /dashboard/today-transactions → See pending payments
2. PATCH /payments/{id}/verify → Verify payment
3. PATCH /orders/{id}/status → Update order status
```

---

## 📱 **FILE UPLOAD TESTING DI POSTMAN:**

### **Cara Upload Bukti Pembayaran:**

1. **Set Method:** `POST`
2. **URL:** `{{base_url}}/payments/1/upload-proof`
3. **Authorization:** `Bearer {{user_token}}`
4. **Body:**
    - Pilih `form-data`
    - Key: `bukti_pembayaran`
    - Type: `File`
    - Value: [Select your image/PDF file]

### **Supported File Types:**

-   ✅ JPG, JPEG
-   ✅ PNG
-   ✅ PDF
-   ⚠️ Max size: 5MB

---

## 💡 **MIGRATION COMMAND:**

Jangan lupa run migration setelah update:

```bash
php artisan migrate:refresh --seed
# atau
php artisan migrate:rollback
php artisan migrate
```

---

## ✅ **KESIMPULAN:**

**Backend sekarang 100% mendukung sistem pembayaran seperti di gambar!**

### **Fitur Yang Ready:**

-   ✅ Checkout dengan QR Code display
-   ✅ Upload bukti pembayaran (drag & drop ready)
-   ✅ Payment status tracking
-   ✅ Admin verification system
-   ✅ File storage & URL generation

### **Migration Updated:**

-   ✅ Single payment method (QRIS only)
-   ✅ Additional fields untuk payment tracking
-   ✅ Optimized untuk sistem seperti gambar

**Siap untuk testing dan implementasi frontend!** 🎯🚀
