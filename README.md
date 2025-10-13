<!-- ...existing content... -->

## Setup Instructions

### 1. Clone Repository

```bash
git clone https://github.com/mbpsha/NT_Coba.git
cd NT_Coba
```

### 2. Install PHP Dependencies (WAJIB)

```bash
composer install
```

### 3. Install Node.js Dependencies (WAJIB)

```bash
npm install
```

### 4. Environment Setup (WAJIB)

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup

```bash
# Configure database in .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# (Optional) Seed database
php artisan db:seed
```

### 6. Build Frontend Assets (WAJIB)

```bash
# Build for production
npm run build

# OR run development with hot reload (recommended untuk development)
npm run dev
```

### 7. Create Storage Link

```bash
php artisan storage:link
```

### 8. Run Application

```bash
# Start Laravel server
php artisan serve

# Access: http://127.0.0.1:8000/dashboard
```

### ⚠️ **URUTAN PENTING:**

1. `composer install` (wajib pertama)
2. `npm install` (wajib kedua)
3. Setup `.env` & `php artisan key:generate`
4. `php artisan migrate`
5. `npm run build` atau `npm run dev`
6. `php artisan serve`

<!-- ...existing content... -->
