# Suspect — Person Intelligence Portal

A **CodeIgniter 3.1.1** web application for managing and querying person intelligence records.
Mirrors the core functionality of the `dramslive` project and shares the same `aiesplus` database.

---

## Requirements

| Dependency      | Version |
|-----------------|---------|
| PHP             | 7.4 +   |
| MySQL / MariaDB | 5.7+    |
| Apache / Nginx  | any     |

---

## Quick Start

### 1. Clone the repository

```bash
git clone <repo-url> suspect
cd suspect
```

### 2. Configure the database

Edit `application/config/database.php`.
Set `hostname`, `username`, `password` for the `aiesplus` database.

The `aiesplus` database is shared with the `dramslive` project.

### 3. Configure the person-ID encryption key

Set the `SUSPECT_PID_KEY` environment variable to the same value as
`hash_key` in dramslive's `application/config/auth.php`.

```apache
# In your Apache VirtualHost:
SetEnv SUSPECT_PID_KEY "your-secret-key-here"
```

```nginx
# In Nginx:
fastcgi_param SUSPECT_PID_KEY "your-secret-key-here";
```

> Without this key person IDs are base64-encoded (development fallback only).

### 4. Apache VirtualHost

```apache
<VirtualHost *:80>
    ServerName ctd.suspect.kpk
    DocumentRoot /path/to/suspect/

    <Directory /path/to/suspect/>
        AllowOverride All
        Options -Indexes
        Require all granted
    </Directory>
</VirtualHost>
```

### 5. Writable permissions

```bash
chmod -R 775 application/cache/
chmod -R 775 application/logs/
```

---

## Authentication

### Manual Login

Standard login at `/auth/login` using username or email + password.

Password hashing supported (dramslive-compatible):

| Format  | Detection                        |
|---------|----------------------------------|
| bcrypt  | Starts with `$2y$` or `$2b$`    |
| SHA-1   | 40-character hex string          |
| MD5     | 32-character hex string (legacy) |

On first successful login with a legacy hash, the password is automatically
upgraded to bcrypt.

### SSO Token Login (from ctd.drams.com)

Append `?accesstoken=<token>` or `?token=<token>` to any URL.

The token is validated against the `user_tokens` table in the `aiesplus` database:
- `token` column must match
- `expires` column must be >= current Unix timestamp

If valid, the user session is established and the request continues.
If invalid or expired, the user is redirected to `/auth/login`.

---

## Person Profile Link (enabled from dramslive)

The person-profile link previously hidden in `dramslive` is now fully
implemented in this project at:

```
/personprofile/person_profile?id=<encrypted_person_id>
```

This is identical to the dramslive URL structure:

```
http://ctd.drams.com/personprofile/person_profile/?id=<encrypted_person_id>
```

---

## Routes

| Method   | URI                                   | Controller                       |
|----------|---------------------------------------|----------------------------------|
| GET/POST | `/auth/login`                         | `Auth::login`                    |
| GET      | `/auth/logout`                        | `Auth::logout`                   |
| GET      | `/`                                   | `Welcome::index` (dashboard)     |
| GET      | `/persons`                            | `Persons::index` (listing)       |
| GET      | `/personprofile/person_profile?id=`   | `Personprofile::person_profile`  |
| GET      | `/api/persons/:id/basic`              | `Api::persons_basic`             |
| GET      | `/api/persons/:id/detailed`           | `Api::persons_detailed`          |
| GET      | `/api/persons/:id/identities`         | `Api::persons_identities`        |
| GET      | `/api/persons/:id/education`          | `Api::persons_education`         |
| GET      | `/api/persons/:id/income`             | `Api::persons_income`            |
| GET      | `/api/persons/:id/banks`              | `Api::persons_banks`             |
| GET      | `/api/persons/:id/assets`             | `Api::persons_assets`            |
| GET      | `/api/persons/:id/mobiles`            | `Api::persons_mobiles`           |
| GET      | `/api/persons/:id/relations`          | `Api::persons_relations`         |
| GET      | `/api/persons/:id/criminal`           | `Api::persons_criminal`          |
| GET      | `/api/persons/:id/affiliations`       | `Api::persons_affiliations`      |
| GET      | `/api/persons/:id/projects`           | `Api::persons_projects`          |
| GET      | `/api/persons/:id/category_history`   | `Api::persons_category_history`  |
| GET      | `/api/persons/:id/reports`            | `Api::persons_reports`           |
| GET      | `/api/persons/search?q=`              | `Api::persons_search`            |

---

## Project Structure

```
application/
  config/
    config.php        — Base URL (auto-detected), sessions, hooks
    database.php      — aiesplus DB credentials (prod + dev environments)
    hooks.php         — SSO token gate hook
    routes.php        — All application routes
    suspects.php      — App-specific config (pid_key, token_ttl)
  controllers/
    Auth.php          — Login / logout
    Welcome.php       — Dashboard landing page
    Persons.php       — Person listing with advanced filters
    Personprofile.php — Person profile (14 tabs)
    Api.php           — JSON endpoints for AJAX tab loading
  hooks/
    Sso_token.php     — Access token gate (post_controller_constructor)
  models/
    User_model.php    — Auth against users table, password verification
    Person_model.php  — Person DB queries + ID encryption/decryption
  views/
    layout/
      header.php          — HTML head, navbar, sidebar
      footer.php          — Closing tags, JS
    auth/
      login.php           — Login form
    persons/
      index.php           — Listing with search & filters
    personprofile/
      person_profile.php  — Tabbed profile page (AJAX loaded)
    welcome_message.php   — Dashboard home

assets/
  css/admin.css       — Professional admin theme CSS
  js/admin.js         — Tab AJAX loading, filter toggle, search

system/               — CodeIgniter 3.1.1 core (do not modify)
index.php             — Application entry point
```

---

## Person Profile Tabs

The `/personprofile/person_profile` page provides 14 tabs, each loaded via AJAX:

| # | Tab Name                | API Endpoint                         |
|---|-------------------------|--------------------------------------|
| 1 | Basic Info              | `/api/persons/:id/basic`             |
| 2 | Detailed Info           | `/api/persons/:id/detailed`          |
| 3 | Identities              | `/api/persons/:id/identities`        |
| 4 | Education               | `/api/persons/:id/education`         |
| 5 | Income Sources          | `/api/persons/:id/income`            |
| 6 | Banks Details           | `/api/persons/:id/banks`             |
| 7 | Asset Details           | `/api/persons/:id/assets`            |
| 8 | Mobiles                 | `/api/persons/:id/mobiles`           |
| 9 | Relations               | `/api/persons/:id/relations`         |
| 10 | Criminal Record        | `/api/persons/:id/criminal`          |
| 11 | Affiliations/Trainings | `/api/persons/:id/affiliations`      |
| 12 | Link with Projects     | `/api/persons/:id/projects`          |
| 13 | Category Change History | `/api/persons/:id/category_history` |
| 14 | Person Reports         | `/api/persons/:id/reports`           |

---

## UI Theme

The application uses a custom professional admin theme (`assets/css/admin.css`).
Bootstrap 4 and Font Awesome 5 are included via CDN `@import` fallbacks for development.

**For production**, download and place the following files locally to avoid CDN dependency:

```
assets/vendor/bootstrap/css/bootstrap.min.css
assets/vendor/bootstrap/js/bootstrap.bundle.min.js
assets/vendor/fontawesome/css/all.min.css
assets/vendor/jquery/jquery.min.js
```

Then:
1. Remove the `@import` lines at the top of `assets/css/admin.css`
2. Update `application/views/layout/header.php` to link local CSS files
3. Update `application/views/layout/footer.php` to load local JS files

---

## Security Notes

- `.htaccess` prevents direct access to `application/` and `system/`
- CSRF protection is configurable in `application/config/config.php`
- Never commit real DB credentials or `SUSPECT_PID_KEY` to version control
- `SUSPECT_PID_KEY` must be set as a server environment variable
- Token validation uses exact-match + expiry check against `user_tokens` table

---

## Database Schema Reference (aiesplus tables used)

| Table                     | Purpose                          |
|---------------------------|----------------------------------|
| `users`                   | Login credentials                |
| `user_tokens`             | SSO access tokens                |
| `persons`                 | Core person records              |
| `person_detail`           | Extended person attributes       |
| `person_identities`       | CNIC, passport, etc.             |
| `person_education`        | Education history                |
| `person_income_sources`   | Income & assets                  |
| `person_bank_details`     | Bank accounts                    |
| `person_asset_details`    | Property & assets                |
| `person_mobiles`          | Mobile phone numbers             |
| `person_relations`        | Family & social relations        |
| `person_criminal_records` | FIR / criminal history           |
| `person_affiliations`     | Organisation/group affiliations  |
| `person_projects`         | Project linkages                 |
| `person_category_history` | Category change audit trail      |
| `person_reports`          | Reports filed against person     |

> Table names are defined as constants in `Person_model.php`. Update them if
> the actual `aiesplus` schema uses different table names.
