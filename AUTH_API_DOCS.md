# Authentication API Documentation

## Login dengan Username atau Email

### Endpoint
```
POST /api/login
```

### Request Body
```json
{
  "login": "string",     // Bisa berupa email atau username
  "password": "string"
}
```

### Response Success (200)
```json
{
  "message": "Login successful",
  "user": {
    "id_user": 1,
    "nama": "Test User",
    "username": "testuser",
    "email": "test@example.com",
    "no_telp": null,
    "alamat": null,
    "role": "user",
    "email_verified_at": null,
    "created_at": "2025-10-08T04:48:12.000000Z",
    "updated_at": "2025-10-08T04:48:12.000000Z"
  },
  "token": "1|abc123def456..."
}
```

### Response Error (401)
```json
{
  "error": "Invalid credentials"
}
```

### Response Validation Error (422)
```json
{
  "message": "The login field is required.",
  "errors": {
    "login": ["The login field is required."],
    "password": ["The password field is required."]
  }
}
```

## Contoh Penggunaan

### Login dengan Email
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "test@example.com",
    "password": "password123"
  }'
```

### Login dengan Username
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "login": "testuser",
    "password": "password123"
  }'
```

## Cara Kerja

1. **Deteksi Format Input**: Sistem akan mengecek apakah field `login` berformat email menggunakan `filter_var($loginField, FILTER_VALIDATE_EMAIL)`
2. **Pencarian User**: 
   - Jika format email valid → cari berdasarkan kolom `email`
   - Jika bukan email → cari berdasarkan kolom `username`
3. **Validasi Password**: Gunakan `Hash::check()` untuk memverifikasi password
4. **Generate Token**: Buat Sanctum token untuk authenticated user

## Frontend Integration (Vue.js)

### Updated Login Component
```vue
<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../api'

const router = useRouter()
const login = ref('')  // Changed from 'email' to 'login'
const password = ref('')
const loading = ref(false)
const errorMessage = ref('')

async function submit() {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await api.post('/login', { 
      login: login.value,     // Send as 'login' field
      password: password.value 
    })
    localStorage.setItem('token', data.token)
    api.defaults.headers.common['Authorization'] = `Bearer ${data.token}`
    router.push({ name: 'dashboard' })
  } catch (err) {
    errorMessage.value = err?.response?.data?.error || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <form @submit.prevent="submit">
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
    
    <!-- Username or Email Input -->
    <input 
      v-model="login"
      type="text"
      placeholder="Enter your email or username"
      required
    />
    
    <!-- Password Input -->  
    <input
      v-model="password"
      type="password"
      placeholder="Enter your password"
      required
    />
    
    <button type="submit" :disabled="loading">
      {{ loading ? 'Logging in...' : 'Login' }}
    </button>
  </form>
</template>

<template>
  <form @submit.prevent="submit">
    <div>
      <label>Email or Username</label>
      <input 
        v-model="login" 
        type="text" 
        required 
        placeholder="Enter your email or username" 
      />
    </div>
    <div>
      <label>Password</label>
      <input v-model="password" type="password" required />
    </div>
    <button type="submit" :disabled="loading">
      {{ loading ? 'Signing in...' : 'Sign In' }}
    </button>
  </form>
  <p v-if="errorMessage">{{ errorMessage }}</p>
</template>
```

## Keamanan

- Password di-hash menggunakan bcrypt
- Token menggunakan Laravel Sanctum
- Rate limiting dapat ditambahkan untuk mencegah brute force
- Input validation untuk mencegah injection attacks

## Backward Compatibility

Endpoint ini menggantikan endpoint lama yang hanya menerima email. Jika Anda masih memiliki kode lama yang mengirim field `email`, Anda perlu mengupdate untuk menggunakan field `login`.