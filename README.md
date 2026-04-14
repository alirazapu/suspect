# Suspect — Person Intelligence Portal

A CodeIgniter 3 web application for managing and querying person intelligence records.

## Requirements

- PHP 7.4+
- Composer
- MySQL / MariaDB

## Setup

### 1. Clone & install dependencies

```bash
git clone <repo-url> suspect
cd suspect
composer install
```

### 2. Configure environment

### Code Refrence 

| <a href="http://ctd.drams.com/personprofile/person_profile/?id=VGdqZEdnK0tyWnc3NCswTkR0NTRzQT09"> <span class=""> <i class="fa fa-inbox"></i> Person Profile &nbsp</span> </a>
we need to implement all this page tabs 
same database auth using dramlive repo

Edit `.env` and set:

| Variable | Description |
|---|---|
| `app.baseURL` | Your site URL (e.g. `http://ctd.suspect.kpk/`) |
| `database.default.hostname` | DB host |
| `database.default.database` | DB name |
| `database.default.username` | DB user |
| `database.default.password` | DB password |
| `DRAMS_HASH_KEY` | Must match `hash_key` in dramslive `application/config/auth.php` |
| `SSO_SECRET` | Shared SSO secret (reserved for future HMAC validation) |

### 3. Apache VirtualHost

Point `DocumentRoot` to the `public/` directory:

```apache
<VirtualHost *:80>
    ServerName ctd.suspect.kpk
    DocumentRoot /path/to/suspect/

    <Directory /path/to/suspect/>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 4. Writable directory permissions

```bash
chmod -R 775 writable/
```

## Routes

| Method | URI | Controller |
|---|---|---|
| GET/POST | `/auth/login` | `Auth::login` |
| GET | `/auth/logout` | `Auth::logout` |
| GET | `/auth/sso` | `Auth::sso` |
| GET | `/persons` | `Persons::index` |
| GET | `/persons/profile?id=<encrypted>` | `Persons::profile` |
| GET | `/api/persons/:id/basic` | `Api::persons_basic` |
| GET | `/api/persons/:id/detailed` | `Api::persons_detailed` |
| GET | `/api/persons/:id/identities` | `Api::persons_identities` |
| GET | `/api/persons/:id/education` | `Api::persons_education` |
| GET | `/api/persons/:id/income` | `Api::persons_income` |
| GET | `/api/persons/:id/banks` | `Api::persons_banks` |
| GET | `/api/persons/:id/assets` | `Api::persons_assets` |
| GET | `/api/persons/:id/mobiles` | `Api::persons_mobiles` |
| GET | `/api/persons/:id/relations` | `Api::persons_relations` |
| GET | `/api/persons/:id/criminal` | `Api::persons_criminal` |
| GET | `/api/persons/:id/affiliations` | `Api::persons_affiliations` |
| GET | `/api/persons/:id/projects` | `Api::persons_projects` |
| GET | `/api/persons/:id/category_history` | `Api::persons_category_history` |
| GET | `/api/persons/:id/reports` | `Api::persons_reports` |
| GET | `/api/persons/search?q=` | `Api::persons_search` |

## Project Structure

```
app/
  Config/        — App, Database, Routes, Filters, Suspects (custom)
  Controllers/   — Auth, Persons, Api, BaseController
  Filters/       — AuthFilter
  Helpers/       — pid_helper (AES-256-CBC PID encrypt/decrypt)
  Models/        — PersonModel
  Views/         — layout/, auth/, persons/
public/          — Web root (index.php, .htaccess)
writable/        — Cache, logs, sessions, uploads
.env             — Local environment config (not committed)
.env.example     — Template for .env
spark            — CI4 CLI tool
```

## Authentication

- Standard login at `/auth/login` using SHA-256 hashed passwords (compatible with dramslive Kohana Auth)
- SSO login at `/auth/sso?token=<token>` using one-time tokens stored in the `users` table
- Session key: `suspect_user`

## Notes

- `vendor/` is excluded from version control
- `.env` is excluded from version control — never commit secrets
- CI_ENVIRONMENT should be `production` on the server
