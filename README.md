# Suspect

Web application for `http://ctd.suspect.kpk` — built with CodeIgniter 3 and AdminLTE 3.

Shares the `aiesplus` MySQL database with the `dramslive` app (same `users` table, same `persons` data).

---

## Requirements

- PHP 7.4+
- MySQL / MariaDB (database: `aiesplus`)
- Apache with `mod_rewrite` enabled
- PHP extensions: `mysqli`, `openssl`, `mbstring`, `json`

---

## Installation

```bash
# 1. Clone the repo
git clone <repo-url> suspect
cd suspect

# 2. Copy environment config
cp .env.example .env
# Edit .env with your values

# 3. Load environment variables (Apache / php-fpm: set via SetEnv or fastcgi_param)
# Or source .env in your shell for CLI testing

# 4. Point Apache VirtualHost DocumentRoot to this directory
# Enable mod_rewrite and AllowOverride All

# 5. Ensure writable directories
chmod -R 777 application/logs application/cache
```

---

## Environment Variables

| Variable            | Default                     | Description                               |
|---------------------|-----------------------------|-------------------------------------------|
| `APP_BASE_URL`      | `http://ctd.suspect.kpk/`   | Full base URL including trailing slash    |
| `DB_HOSTNAME`       | `localhost`                 | MySQL hostname                            |
| `DB_USERNAME`       | `root`                      | MySQL username                            |
| `DB_PASSWORD`       | *(empty)*                   | MySQL password                            |
| `DB_DATABASE`       | `aiesplus`                  | MySQL database name                       |
| `SSO_SECRET`        | *(empty)*                   | Shared secret for SSO token validation    |
| `CI_ENCRYPTION_KEY` | `suspect-default-key-...`   | CodeIgniter session encryption key        |
| `CI_ENV`            | `production`                | CI environment (`development`/`production`) |

---

## SSO Flow

Dramslive generates a one-time `login_token` in the `users` table and redirects to:

```
http://ctd.suspect.kpk/auth/sso?token=<TOKEN>[&pid=<ENC_PID>][&return=<URL>]
```

Suspect:
1. Looks up the `login_token` in the shared `users` table
2. Checks token expiry (`token_expires` field)
3. Nullifies the token (one-time use)
4. Creates a session and redirects to the person profile (if `pid` given) or `/persons`

---

## Available Routes

| Method | URL                                   | Description                   |
|--------|---------------------------------------|-------------------------------|
| GET    | `/auth/login`                         | Login page                    |
| POST   | `/auth/login`                         | Process login                 |
| GET    | `/auth/logout`                        | Destroy session, redirect     |
| GET    | `/auth/sso?token=&pid=&return=`       | SSO token login               |
| GET    | `/persons`                            | Persons listing with filters  |
| GET    | `/persons/profile?id=<enc_pid>`       | Person profile (tabbed)       |
| GET    | `/api/persons/{id}/basic`             | JSON: basic info              |
| GET    | `/api/persons/{id}/detailed`          | JSON: detailed info           |
| GET    | `/api/persons/{id}/identities`        | JSON: CNIC / identities       |
| GET    | `/api/persons/{id}/education`         | JSON: education records       |
| GET    | `/api/persons/{id}/income`            | JSON: income sources          |
| GET    | `/api/persons/{id}/banks`             | JSON: bank accounts           |
| GET    | `/api/persons/{id}/assets`            | JSON: assets                  |
| GET    | `/api/persons/{id}/mobiles`           | JSON: mobile numbers          |
| GET    | `/api/persons/{id}/relations`         | JSON: family relations        |
| GET    | `/api/persons/{id}/criminal`          | JSON: criminal records        |
| GET    | `/api/persons/{id}/affiliations`      | JSON: affiliations/trainings  |
| GET    | `/api/persons/{id}/projects`          | JSON: linked projects         |
| GET    | `/api/persons/{id}/category_history`  | JSON: category change history |
| GET    | `/api/persons/{id}/reports`           | JSON: person reports          |
| GET    | `/api/persons/search?q=`              | JSON: typeahead search        |

---

## Theme

[AdminLTE 3](https://adminlte.io/) (Bootstrap 4-based), loaded via CDN.
