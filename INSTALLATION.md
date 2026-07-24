# Chapman Media — Installation Guide

This guide walks through setting up the Chapman Media application locally or on a server. The app is a Laravel application for managing events, collecting media release forms, and administering users.

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.1 or higher |
| Composer | 2.x |
| Node.js | 18.x or higher (20+ recommended) |
| npm | 9.x or higher |

### PHP extensions

Enable the following extensions before installing:

- `bcmath`
- `ctype`
- `curl`
- `dom`
- `fileinfo`
- `json`
- `mbstring`
- `openssl`
- `pdo` (with `pdo_sqlite` and/or `pdo_mysql`)
- `session`
- `tokenizer`
- `xml`
- `zlib`

Optional but recommended for development:

- `intl`
- `pcntl` (queue workers)

## Quick start (local development)

From the project root:

```bash
composer setup
```

This command will:

1. Install PHP dependencies
2. Copy `.env.example` to `.env` if needed
3. Generate an application key
4. Run database migrations
5. Install npm dependencies
6. Build frontend assets

Then start the development environment:

```bash
composer dev
```

This runs the web server, queue worker, log viewer, and Vite dev server together.

Open [http://localhost:8000](http://localhost:8000) in your browser.

## Manual installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd media
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your local settings. At minimum, review:

- `APP_NAME` — application display name
- `APP_URL` — public URL of the app (e.g. `http://localhost:8000`)
- `DB_*` — database connection (SQLite is the default for local development)
- `MAIL_*` — mail delivery settings (see [Mail configuration](#mail-configuration))

### 4. Prepare the database

**SQLite (default)**

```bash
touch database/database.sqlite
php artisan migrate
```

**MySQL / MariaDB**

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chapman_media
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run migrations:

```bash
php artisan migrate
```

### 5. Seed a local admin user (optional)

For local development without SAML, seed a test admin account:

```bash
php artisan db:seed
```

Default credentials:

| Field | Value |
|-------|-------|
| Email | `admin@test.local` |
| Password | `password` |

Change this password immediately in any shared or production environment.

### 6. Link public storage

The login page and site footer reference images under `storage/app/public/imgs/`. Create the symlink:

```bash
php artisan storage:link
```

Add branding assets to `storage/app/public/imgs/` as needed (for example `Night-Campus-79.jpg` and `svg-image-3.svg`).

### 7. Build frontend assets

For development with hot reload:

```bash
npm run dev
```

For production or a one-time local build:

```bash
npm run build
```

### 8. Run the application

**Development (single process)**

```bash
php artisan serve
```

**Development (full stack)**

```bash
composer dev
```

The app will be available at the URL shown by `php artisan serve` (default: `http://127.0.0.1:8000`).

## SAML 2.0 single sign-on

Production authentication uses Chapman University SAML SSO via `scaler-tech/laravel-saml2`.

### 1. Configure environment variables

Set these values in `.env`:

```env
SAML2_TENANT_KEY=chapman
SAML2_IDP_ENTITY_ID=
SAML2_IDP_LOGIN_URL=
SAML2_IDP_LOGOUT_URL=
SAML2_IDP_X509_CERT=
SAML2_LOGIN_URL=/my-events
SAML2_LOGOUT_URL=/login
SAML2_ERROR_URL=/login
```

Paste the IdP X.509 certificate as a single line or multi-line PEM block.

### 2. Sync the SAML tenant

```bash
php artisan saml2:sync-tenant --show-credentials
```

Copy the printed `SAML2_TENANT_UUID` value into `.env`.

### 3. Register the service provider with your IdP

Use the output from `saml2:sync-tenant --show-credentials` to configure:

- SP metadata URL
- ACS (Assertion Consumer Service) URL
- Entity ID

SAML routes are served under `/saml2/{uuid}/`.

### Local development without SAML

Use the email/password login at `/login` with a seeded admin user, or register a new account at `/register`.

## Mail configuration

Submission confirmation emails are sent when a media release form is completed. Configure mail in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=media@chapman.edu
MAIL_FROM_NAME="Chapman Media"
```

For local development, `MAIL_MAILER=log` writes messages to `storage/logs/laravel.log` instead of sending them.

The confirmation email subject can be customized in `config/media_release.php`.

## Production deployment

### Environment checklist

Set these before going live:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-production-domain
APP_FORCE_HTTPS=true

SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

TRUSTED_PROXIES=*

SECURITY_HSTS_ENABLED=true
```

Use a production database (`mysql` or `pgsql`), not SQLite.

### Deploy steps

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Web server

Point the document root to the `public/` directory. Ensure:

- `public/` is the only web-accessible directory
- `storage/` and `bootstrap/cache/` are writable by the web server user
- HTTPS is terminated at the load balancer or web server

### Queue worker (optional)

The app sends confirmation mail synchronously by default. If you move mail or other work to the queue, run a worker:

```bash
php artisan queue:work
```

Set `QUEUE_CONNECTION=database` (or `redis`) in `.env` and ensure the `jobs` table exists (`php artisan queue:table` + migrate) if using the database driver.

## Application overview

After installation, these are the main areas of the app:

| URL | Access | Description |
|-----|--------|-------------|
| `/my-events` | Authenticated users | View and manage events, submit media release forms |
| `/events/create` | Authenticated users | Create a new event |
| `/dashboard/media-releases` | Admin | View all media release submissions |
| `/dashboard/users` | Admin | Manage users |
| `/login` | Public | Sign in (SAML or local credentials) |

### User roles

- **Admin** — full access to dashboard, users, and all submissions
- **User** — can create and manage their own events and view related submissions

## Running tests

```bash
composer test
```

Or directly:

```bash
php artisan test
```

## Troubleshooting

### `SQLSTATE[HY000]`: database file does not exist

Create the SQLite database file:

```bash
touch database/database.sqlite
php artisan migrate
```

### Login page images are missing

Run `php artisan storage:link` and add files to `storage/app/public/imgs/`.

### SAML login fails

1. Confirm `SAML2_TENANT_UUID` matches the tenant created by `saml2:sync-tenant`
2. Verify IdP URLs and certificate in `.env`
3. Check `storage/logs/laravel.log` with `SAML2_DEBUG=true` (disable in production)

### Permission errors on `storage/` or `bootstrap/cache/`

```bash
chmod -R ug+rwx storage bootstrap/cache
```

Adjust ownership for your web server user if needed.

### Frontend changes not appearing

Rebuild assets:

```bash
npm run build
```

Or run `npm run dev` during development.

## Support

For application issues, open an issue in the GitHub repository or contact the Chapman Media development team.
