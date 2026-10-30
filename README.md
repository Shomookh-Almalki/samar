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

## Share the site online (temporary link, for a demo)
1. Stop `composer run dev` (Ctrl + C) and build the assets once: `npm run build`
2. Run `php artisan serve`
3. In a second terminal: `cloudflared tunnel --url http://localhost:8000` (install it with `winget install --id Cloudflare.cloudflared`)
4. Share the `https://....trycloudflare.com` link it prints. It works only while your laptop and both terminals stay on.

Before sharing publicly, create the database **without** demo accounts (`php artisan migrate:fresh`) and set `APP_ENV=production` and `APP_DEBUG=false` in `.env`.

## Publish on Render (permanent link)
The project already contains `Dockerfile`, `docker/start.sh` and `render.yaml` (a Render Blueprint: a web service + a free Postgres database).

1. Push the project to a GitHub repository.
2. Generate the app key on your computer and copy the whole output (it starts with `base64:`):
   `php artisan key:generate --show`
3. On render.com: **New → Blueprint**, choose your repository, and paste the key when it asks for `APP_KEY`. Press **Apply**.
4. Wait for the first build (several minutes). Your site is then at `https://samar-xxxx.onrender.com`.

Notes: on the free plan the site sleeps after 15 minutes without visits (the next visit takes about a minute to wake it), and the free database is limited in time, so check Render's current free-plan terms.
The database is created empty (no demo accounts).

## Features
- Sign up, log in, log out (passwords are hashed)
- Share, edit and delete your own quotes (3-155 characters), with the **poet** (optional) and the **era** (required)
- **Sections:** browse the quotes by era (Pre-Islamic, Early Islamic, Umayyad, Abbasid, Andalusian, Mamluk & Ottoman, Modern)
- **Likes:** like / unlike a quote (one like per user); a guest who presses the heart goes to the log in page
- **Quote of the Day (بيت اليوم):** the most liked quote, and you can like it from its box

## Database
`database/migrations`: users, eras, quotes, likes.

