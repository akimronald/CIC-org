# Davizo - Minimal Backend

This repository shipped with a tiny Node/Express backend for local demos, but this folder now also includes a small PHP + MySQL backend you can use instead of the file-based Node server.

Two backend options:
- Node/Express (existing): uses `server.js` and JSON files for persistence.
- PHP + MySQL (new): small set of endpoints under the `api/` folder that persist to a MySQL/MariaDB database.

Files added for the PHP backend:
- `api/config.php`   : database connection and shared helpers
- `api/orders.php`   : POST endpoint to create an order
- `api/contact.php`  : POST endpoint to submit a contact message
- `api/apply.php`    : POST endpoint to submit a job application
- `api/status.php`   : simple status endpoint
- `db.sql`           : SQL schema to create the `davizo` database and tables

PHP + MySQL setup (Windows / PowerShell)

1) Install PHP and a MySQL-compatible server (MySQL or MariaDB). "XAMPP" or "Laragon" are simple bundles that work well on Windows.

2) Import the schema. In PowerShell (adjust paths if needed):

```powershell
cd C:\Davizo
mysql -u root -p < db.sql
```

3) Place the `api/` folder where your webserver can serve PHP files. If you're using the included static files, you can run PHP's built-in server from the repo root for testing:

```powershell
cd C:\Davizo
php -S localhost:8000 -t .
```

4) Configure database credentials. `api/config.php` reads DB connection settings from environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` or falls back to sensible defaults. Example (PowerShell):

```powershell
$env:DB_HOST = '127.0.0.1'; $env:DB_NAME='davizo'; $env:DB_USER='root'; $env:DB_PASS='yourpassword'
php -S localhost:8000 -t .
```

5) Update the frontend fetch URLs if needed. The PHP endpoints are:

- POST /api/orders.php
- POST /api/contact.php
- POST /api/apply.php
- GET  /api/status.php

Quick notes and security
- These endpoints are minimal and intended for local development only.
- For production you should: validate and sanitize all inputs, add authentication/CSRF protection, use parameterized queries (already used), enable TLS, and restrict CORS.

