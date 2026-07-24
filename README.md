# Chapman Media

Chapman Media is a Laravel application for Chapman University's Strategic Marketing & Communications team. It supports event management, on-site media release form collection, and administrative review of submissions.

## Features

- Event creation and management for authorized staff
- Media release form submission with photo capture and digital signature
- Role-based access (`admin` and `user`)
- SAML 2.0 single sign-on (Chapman University IdP)
- Email confirmation with a copy of submitted form data
- Admin dashboard for users and media release submissions

## Tech stack

- [Laravel 10](https://laravel.com/) (PHP 8.1+)
- Blade templates with custom CSS
- Vite + Tailwind CSS (build tooling)
- SQLite (local) or MySQL/MariaDB (production)
- SAML 2.0 via [scaler-tech/laravel-saml2](https://github.com/scaler-tech/laravel-saml2)

## Installation

See **[INSTALLATION.md](INSTALLATION.md)** for full setup instructions, including:

- Local development quick start
- Database and environment configuration
- SAML SSO setup
- Mail configuration
- Production deployment checklist

### Quick start

```bash
composer setup
composer dev
```

Visit [http://localhost:8000](http://localhost:8000).

For local login without SAML:

```bash
php artisan db:seed
```

Then sign in at `/login` with `admin@test.local` / `password`.

## Project structure

```
app/
  Http/Controllers/   # Web controllers
  Mail/               # Confirmation email
  Models/             # Eloquent models
  Services/           # Image encryption and related services
config/               # Application and SAML configuration
database/migrations/  # Database schema
public/               # Web root (CSS, JS)
resources/views/      # Blade templates
routes/web.php        # Application routes
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
