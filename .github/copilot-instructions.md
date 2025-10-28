# Copilot Instructions for NT2 Codebase

## Overview
- **Backend:** Laravel (PHP) in the root directory, following standard Laravel structure.
- **Frontend:** Vue 3 app in `frontend/` using Vite.
- **Separation:** Backend and frontend are decoupled; backend serves API and web, frontend is a SPA built separately.

## Key Directories & Files
- `app/Http/Controllers/` — Laravel controllers (API, web logic)
- `app/Models/` — Eloquent ORM models (e.g., `User.php`)
- `routes/web.php` — Web routes (Blade, controllers)
- `routes/console.php` — Artisan CLI commands
- `resources/views/` — Blade templates
- `frontend/src/` — Vue 3 source code
- `frontend/vite.config.js` — Vite config for frontend
- `public/` — Laravel public entry (`index.php`), static assets

## Developer Workflows
- **Backend:**
  - Run server: `php artisan serve`
  - Run tests: `vendor\bin\phpunit` or `php artisan test`
  - Migrate DB: `php artisan migrate`
- **Frontend:**
  - Install deps: `cd frontend && npm install`
  - Dev server: `cd frontend && npm run dev`
  - Build: `cd frontend && npm run build`

## Project Conventions
- **Backend:**
  - Use Eloquent for DB access; migrations in `database/migrations/`
  - Seeders/factories in `database/seeders/` and `database/factories/`
  - Service providers in `app/Providers/`
  - Config in `config/`
- **Frontend:**
  - Vue SFCs in `frontend/src/components/`
  - Main entry: `frontend/src/main.js`, root: `frontend/src/App.vue`
  - Styles: `frontend/src/style.css`

## Integration Points
- **API:**
  - Backend exposes API endpoints (see `routes/web.php` and controllers)
  - Frontend fetches data from backend API (URL/config may need adjustment for local/prod)
- **Assets:**
  - Built frontend assets can be served via Laravel `public/` if needed

## Patterns & Examples
- **User model:** `app/Models/User.php`
- **Example migration:** `database/migrations/0001_01_01_000000_create_users_table.php`
- **Example Vue component:** `frontend/src/components/`

## Tips for AI Agents
- Respect the backend/frontend separation; do not mix PHP and JS logic
- Use Laravel and Vue CLI tools for scaffolding and running tasks
- Reference existing files for structure and naming
- Prefer updating existing conventions over introducing new ones unless justified

---
_If any section is unclear or missing, please provide feedback for improvement._
