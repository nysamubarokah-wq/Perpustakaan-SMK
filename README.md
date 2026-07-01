# PerpustakaanDigital

Digital library system built with Laravel 13. Manages books, members, borrowing, e-books, and VIP memberships.

## Tech Stack

- **Framework:** Laravel 13
- **Database:** SQLite
- **PDF:** DomPDF (barryvdh/laravel-dompdf)
- **Auth:** Laravel Breeze
- **Frontend:** Vite + Tailwind CSS

## Features

### Member Features
- Browse and search book catalog
- Borrow books (request → admin approval)
- Return books with admin confirmation
- Favorite books
- Write and manage reviews
- Read and purchase e-books with virtual coins
- VIP membership system
- Customizable profile with backgrounds

### Admin Features
- Dashboard with statistics
- Book management (CRUD, bulk delete, import)
- Member management
- Borrowing request approvals
- Return request approvals
- User role management
- E-book management
- VIP user management
- Review moderation
- PDF report export

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run dev
php artisan serve
```

## Routes Overview

| Route | Description |
|-------|-------------|
| `/dashboard` | Member dashboard |
| `/koleksi` | Book collection |
| `/ebook` | E-book catalog |
| `/favorit` | Favorites list |
| `/profil` | User profile |
| `/admin/dashboard` | Admin dashboard |
| `/admin/buku` | Book management |
| `/admin/anggota` | Member management |
| `/admin/peminjaman` | Borrowing management |
| `/admin/laporan/pdf` | Export PDF report |

## License

MIT