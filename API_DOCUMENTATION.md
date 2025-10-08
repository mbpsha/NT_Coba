# Laravel Breeze API Documentation

Proyek Laravel ini sekarang menggunakan **Laravel Breeze API** untuk authentication backend yang akan berkomunikasi dengan frontend Vue.js.

## 🚀 Setup Completed

✅ Laravel Breeze terinstall dengan API scaffolding  
✅ Laravel Sanctum terintegrasi untuk API token authentication  
✅ CORS dikonfigurasi untuk frontend Vue.js  
✅ Database migrations sudah dijalankan  
✅ User model menggunakan HasApiTokens trait

## 📡 Available API Endpoints

Base URL: `http://localhost:8000/api`

### Authentication Endpoints

| Method | Endpoint    | Description                      | Body Parameters                                      |
| ------ | ----------- | -------------------------------- | ---------------------------------------------------- |
| POST   | `/register` | Register user baru               | `name`, `email`, `password`, `password_confirmation` |
| POST   | `/login`    | Login user                       | `email`, `password`                                  |
| POST   | `/logout`   | Logout user (authenticated)      | -                                                    |
| GET    | `/user`     | Get user profile (authenticated) | -                                                    |

### Password Reset Endpoints

| Method | Endpoint           | Description            | Body Parameters                                       |
| ------ | ------------------ | ---------------------- | ----------------------------------------------------- |
| POST   | `/forgot-password` | Request password reset | `email`                                               |
| POST   | `/reset-password`  | Reset password         | `token`, `email`, `password`, `password_confirmation` |

### Email Verification Endpoints

| Method | Endpoint                           | Description               |
| ------ | ---------------------------------- | ------------------------- |
| GET    | `/verify-email/{id}/{hash}`        | Verify email address      |
| POST   | `/email/verification-notification` | Resend verification email |

## 🔐 Authentication Flow

1. **Register**: POST `/api/register` - Creates new user and returns authentication token
2. **Login**: POST `/api/login` - Authenticates user and returns token
3. **Authenticated Requests**: Include `Authorization: Bearer {token}` header
4. **Logout**: POST `/api/logout` - Invalidates current token

## 🌐 CORS Configuration

-   **Frontend URL**: `http://localhost:5173` (Vue.js development server)
-   **Stateful domains**: localhost, 127.0.0.1 dengan berbagai port
-   **Credentials support**: Enabled untuk cookie-based sessions

## 💾 Database

-   SQLite database: `database/database.sqlite`
-   Migrations sudah dijalankan
-   Tables: users, password_reset_tokens, cache, jobs, personal_access_tokens

## 🔧 Environment Variables

```
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:5173
```

## 🚀 Running the Application

```bash
# Start Laravel server
php artisan serve

# Start Vue.js development server (di folder vite-project)
cd vite-project
npm run dev
```

## 📝 Example API Usage

### Register User

```bash
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login User

```bash
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

### Get User Profile

```bash
GET /api/user
Authorization: Bearer {your-token-here}
```

## 🎯 Next Steps untuk Vue.js Frontend

1. Install Axios untuk HTTP requests di Vue.js
2. Create authentication store/composables
3. Implement login/register forms
4. Setup token management dan axios interceptors
5. Add route guards untuk protected pages

---

**Note**: Pastikan Laravel server running di port 8000 dan Vue.js development server di port 5173 untuk CORS configuration yang optimal.
