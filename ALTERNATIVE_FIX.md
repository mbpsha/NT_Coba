# ⚠️ ALTERNATIVE FIX - Kalau Masih 403

## Option 1: Ganti SESSION_DRIVER ke Cookie

Update `.env` di hosting:

```env
SESSION_DRIVER=cookie
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=none
```

**Kenapa?**

-   `cookie` driver simpan session di browser cookie (lebih reliable di shared hosting)
-   `SESSION_SAME_SITE=none` allow cross-site requests (kalau ada subdomain)

Clear cache:

```bash
php artisan config:clear && php artisan cache:clear
```

---

## Option 2: Cek .htaccess Rules

Pastikan file `public/.htaccess` ada:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## Option 3: Disable CSRF untuk Testing

**HANYA UNTUK DEBUG!** Buat middleware baru:

File: `app/Http/Middleware/DisableCsrfForRoutes.php`

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableCsrfForRoutes
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
```

Register di `bootstrap/app.php`:

```php
$middleware->web(append: [
    \App\Http\Middleware\HandleInertiaRequests::class,
])->except([
    'cart.update.qty'  // Disable CSRF untuk route ini aja
]);
```

**⚠️ HAPUS setelah testing!**

---

## Option 4: Force Session Regeneration

Tambahin di `AuthController@login` setelah login berhasil:

```php
Auth::login($user);
$request->session()->regenerate();  // ← TAMBAH INI
$request->session()->save();        // ← TAMBAH INI
```

---

## Option 5: Check Hosting PHP Settings

Di cPanel/Hosting panel, cek PHP settings:

-   `session.cookie_httponly` = On
-   `session.cookie_secure` = Off (kalau SESSION_SECURE_COOKIE=false)
-   `session.use_strict_mode` = On
-   `session.gc_maxlifetime` = 7200 (2 jam)

---

## Debug Script

Bikin file `public/debug-session.php`:

```php
<?php
session_start();
$_SESSION['test'] = 'works';

echo "Session ID: " . session_id() . "\n";
echo "Session Data: " . print_r($_SESSION, true) . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
echo "Session Cookie: " . print_r($_COOKIE, true) . "\n";
?>
```

Akses `https://tandur.online/debug-session.php`

-   Kalau session ID berubah tiap refresh = masalah!
-   Kalau session data hilang = permission issue!

---

## Final Resort: Contact Hosting Support

Kalau semua gagal, hosting lu kemungkinan ada restriction khusus:

1. Session save path tidak writable
2. PHP session module disabled
3. ModSecurity blocking session cookies
4. CloudFlare/WAF interfering

Tanya ke support:

> "Session Laravel tidak persist setelah login. Apakah ada restriction untuk session handling atau perlu whitelist something?"

---

## Priority Order (coba satu-satu):

1. ✅ **SESSION_DRIVER=cookie** + **SESSION_SAME_SITE=none**
2. ✅ Check `.htaccess` Authorization Header
3. ✅ `session()->regenerate()` di login
4. ✅ Debug script untuk test native PHP session
5. ⚠️ Contact hosting support
