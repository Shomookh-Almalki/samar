# Samar · سمر

A Laravel + Blade website for **Arabic poetry** (Arabic interface, right to left, verses in a Ruq'ah handwriting font). Anyone can read the poetic quotes (verses) and browse them by era. Registered users can share their own verses, choose the poet and the era before publishing, edit or delete them, and like their favourites. The most liked verse is shown as the **Quote of the Day**.

## Run the project

```bash
composer install
npm install
cp .env.example .env        # Windows: copy .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed   # creates the SQLite database with demo verses
composer run dev            # then open http://127.0.0.1:8000
```

Demo accounts (created by `--seed`): `layla@example.com`, `omar@example.com`, `noura@example.com` - password: `password`.
Use `php artisan migrate:fresh` (without `--seed`) for an empty database.

Run the tests with `php artisan test`.

## Features
- Sign up, log in, log out (passwords are hashed)
- Share, edit and delete your own quotes (3-155 characters), with the **poet** (optional) and the **era** (required)
- **Sections:** browse the quotes by era (Pre-Islamic, Early Islamic, Umayyad, Abbasid, Andalusian, Mamluk & Ottoman, Modern)
- **Likes:** like / unlike a quote (one like per user); a guest who presses the heart goes to the log in page
- **Quote of the Day (بيت اليوم):** the most liked quote, and you can like it from its box

## Database
`database/migrations`: users, eras, quotes, likes.

