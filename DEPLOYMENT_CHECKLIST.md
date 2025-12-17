# � URGENT FIX - Session Authentication di Hosting

## ⚠️ MASALAH UTAMA

**Error:** `403 | UNAUTHORIZED CART ACCESS`
**Root Cause:** Session tidak persist setelah login di hosting → Auth::id() = null

---

## 🆕 FINAL FIX - December 17, 2025

### 🔧 Changes Applied

1. **✅ TrustProxies Middleware** - [app/Http/Middleware/TrustProxies.php](app/Http/Middleware/TrustProxies.php)

    - Diperlukan karena hosting pakai reverse proxy/load balancer
    - Trust all proxies dengan header X-Forwarded-\*

2. **✅ Bootstrap App Config** - [bootstrap/app.php](bootstrap/app.php)

    - Register `trustProxies(at: '*')` di middleware

3. **✅ Session Driver Change** - [.env.production.example](.env.production.example)

    - Ganti dari `database` ke `file`
    - `SESSION_SECURE_COOKIE=false` (coba dulu, nanti true kalau udah jalan)

4. **✅ Debug Route** - [routes/web.php](routes/web.php)
    - Route `/debug-session` untuk cek session status
    - **WAJIB HAPUS** setelah testing!

---

## 📦 DEPLOYMENT STEPS (URUT!)

### Step 1: Upload Files ke Hosting

```
✅ app/Http/Middleware/TrustProxies.php    ← FILE BARU!
✅ bootstrap/app.php
✅ routes/web.php
✅ public/build/  ← SEMUA ISI FOLDER!
```

### Step 2: Update .env di Hosting

Copy dari [.env.production.example](.env.production.example):

```env
# CRITICAL CHANGES!
SESSION_DRIVER=file              ← GANTI DARI database!
SESSION_SECURE_COOKIE=false      ← GANTI DARI true!
SESSION_DOMAIN=null
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### Step 3: Fix Permissions di Hosting

```bash
chmod -R 775 storage/
chmod -R 775 storage/framework/
chmod -R 775 storage/framework/sessions/
chmod -R 775 bootstrap/cache/
```

### Step 4: Clear Cache via SSH

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Step 5: Test Debug Route

Buka di browser (setelah login):

```
https://tandur.online/debug-session
```

**Expected Output:**

```json
{
  "auth_check": true,
  "auth_id": 123,
  "user": { "id_user": 123, "email": "test@test.com", "username": "test" },
  "session_id": "some-long-hash",
  "session_driver": "file",
  "session_data": { ... }
}
```

**Kalau auth_check = false atau auth_id = null:**
→ Session masih gagal! Lanjut ke Step 6.

### Step 6: Kalau Masih Gagal

Coba config ini di `.env`:

```env
SESSION_DRIVER=cookie           ← Last resort!
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=null
SESSION_SAME_SITE=none          ← Ubah dari lax
```

Lalu:

```bash
php artisan config:clear && php artisan cache:clear
```

---

## 🧪 Testing Flow

1. **Clear browser** cache & cookies (tandur.online)
2. **Login** ke akun
3. **Akses** `/debug-session` → Cek `auth_check` harus `true`
4. **Tambah produk** ke cart
5. **Klik +/-** di cart → Harusnya TIDAK 403 lagi!
6. **Checkout** → Cart kosong

---

## 🗑️ HAPUS DEBUG ROUTE!

Setelah testing berhasil, hapus route ini dari [routes/web.php](routes/web.php):

```php
// DEBUG ROUTE - HAPUS SETELAH TESTING!
Route::get('/debug-session', function() { ... });
```

---

# 🚀 Deployment Checklist - Fix Cart Update 403 Forbidden

## ✅ Yang Udah Dibenerin (Lengkap)

### 1. **Frontend Changes**

-   ✅ [resources/js/Pages/User/Cart.vue](resources/js/Pages/User/Cart.vue) - Ganti `axios` jadi `router.post` (Inertia)
-   ✅ [resources/js/Pages/User/CheckoutCart.vue](resources/js/Pages/User/CheckoutCart.vue) - Udah pakai `router.post`
-   ✅ [resources/js/Pages/User/Checkout.vue](resources/js/Pages/User/Checkout.vue) - Udah pakai `router.post`
-   ✅ [resources/js/app.js](resources/js/app.js) - Tambahin explicit CSRF token setup
-   ✅ [resources/js/bootstrap.js](resources/js/bootstrap.js) - CSRF token untuk axios udah ada

### 2. **Backend Changes**

-   ✅ [app/Http/Controllers/CartController.php](app/Http/Controllers/CartController.php) - Tambahin debug logging & better error handling
-   ✅ [routes/web.php](routes/web.php) - Import `Auth` facade, fix helper function
-   ✅ [app/Http/Middleware/HandleInertiaRequests.php](app/Http/Middleware/HandleInertiaRequests.php) - Middleware udah bener
-   ✅ [bootstrap/app.php](bootstrap/app.php) - Middleware configuration OK

### 3. **Configuration**

-   ✅ [.env.production.example](.env.production.example) - Template production environment
-   ✅ [config/session.php](config/session.php) - Session config udah bener
-   ✅ [config/cors.php](config/cors.php) - CORS `supports_credentials` enabled
-   ✅ [resources/views/app.blade.php](resources/views/app.blade.php) - CSRF meta tag ada

---

## 📦 Deployment Steps

### Step 1: Build Assets

```bash
npm run build
```

**Status: ✅ DONE** (Exit Code 0)

### Step 2: Upload ke Hosting

Upload file-file ini ke hosting:

```
✅ app/Http/Controllers/CartController.php
✅ routes/web.php
✅ resources/js/Pages/User/Cart.vue
✅ resources/js/app.js
✅ public/build/  ← SEMUA ISI FOLDER INI!
```

### Step 3: Update .env di Hosting

Copy dari [.env.production.example](.env.production.example), **PENTING** yang ini:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tandur.online

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null          ← HARUS null!
SESSION_SECURE_COOKIE=true   ← HTTPS wajib true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### Step 4: SSH ke Hosting & Run Commands

```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Pastikan sessions table ada
php artisan migrate --force

# Cache ulang (optional)
php artisan config:cache
php artisan route:cache
```

### Step 5: Permissions Check

Pastikan writable:

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Step 6: Database Check

Cek table `sessions` ada di database:

```sql
SHOW TABLES LIKE 'sessions';
SELECT * FROM sessions LIMIT 1;
```

### Step 7: Browser Testing

1. **Clear browser cache & cookies** (tandur.online)
2. **Hard refresh**: Ctrl + Shift + R
3. **Login** dengan user baru atau existing
4. **Tambah produk** ke cart
5. **Update qty** (+ / -)
6. **Checkout** - pastikan cart kosong setelahnya

---

## 🐛 Debug - Kalau Masih 403

### Cek Log di Hosting

```bash
tail -f storage/logs/laravel.log
```

Cari error message:

```
Cart ownership mismatch
```

### Kalau Ada Mismatch

Berarti **session tidak persist**. Coba:

1. **Ganti SESSION_DRIVER** dari `database` ke `file`:

    ```env
    SESSION_DRIVER=file
    ```

2. **Cek .htaccess** (kalau pakai Apache):

    ```apache
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteCond %{HTTPS} !=on
        RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    </IfModule>
    ```

3. **Trust Proxies** - Kalau hosting pakai reverse proxy/load balancer, bikin file:

    ```php
    // app/Http/Middleware/TrustProxies.php
    <?php
    namespace App\Http\Middleware;

    use Illuminate\Http\Middleware\TrustProxies as Middleware;

    class TrustProxies extends Middleware
    {
        protected $proxies = '*';
        protected $headers =
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO;
    }
    ```

    Lalu register di `bootstrap/app.php`:

    ```php
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
        \App\Http\Middleware\TrustProxies::class,  // TAMBAH INI
    ]);
    ```

---

## 📊 Summary

### Root Cause

**403 Forbidden** disebabkan oleh:

1. ~~CSRF token gagal~~ ✅ Fixed (pakai `router.post`)
2. **Session tidak persist** di hosting → Auth check gagal di CartController

### Solution Applied

1. ✅ Ganti axios ke Inertia `router.post` (auto CSRF)
2. ✅ Setup session config untuk HTTPS
3. ✅ Tambahin debug logging di CartController
4. ✅ Fix session domain (null = auto-detect)

### Expected Result

-   ✅ Update qty cart berhasil
-   ✅ Login tetap persist
-   ✅ Cart kosong setelah checkout
-   ✅ No more 403 Forbidden!

---

## 📝 Notes

-   Build terakhir: npm run build (Exit Code 0)
-   Database: tanz5336_Tandur
-   Hosting: tandur.online (HTTPS)
-   Laravel Version: 11.x
-   Session Driver: database (recommended) atau file (fallback)

---

**Good luck bang! 🚀**
