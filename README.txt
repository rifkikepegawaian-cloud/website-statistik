# Offline (No-CDN) Overlay for Laravel 12

This overlay moves all JS/CSS to Vite-bundled local assets. No CDN at runtime.

## Steps

1) Ensure a fresh Laravel 12 app in `web-statistik`:
   composer create-project laravel/laravel web-statistik "12.*"
   cd web-statistik
   php artisan key:generate

2) Copy this overlay into project root (merge/overwrite).

3) Install NPM deps (one time):
   npm install
   npm install -D tailwindcss postcss autoprefixer
   npm install chart.js chartjs-plugin-datalabels xlsx

4) Initialize Tailwind (only once):
   npx tailwindcss init -p
   (config files are already provided here; this is optional if they exist)

5) Build assets (dev or prod):
   npm run dev
   # or
   npm run build

6) Laravel side:
   php artisan migrate
   php artisan db:seed --class=Database\\Seeders\\AdminUserSeeder
   php artisan storage:link

7) Run:
   php artisan serve

- Home:      /
- Admin:     /login  (admin / admin123)

