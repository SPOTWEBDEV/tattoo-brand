# Bare Skin Studio — backend

Pure PHP (no framework) + MySQL via PDO. No Composer, no external
libraries — `mail()` is used directly for sending replies.

## Setup

1. Create a database and load the schema:
   ```
   mysql -u root -p -e "CREATE DATABASE bare_skin_studio CHARACTER SET utf8mb4"
   mysql -u root -p bare_skin_studio < schema.sql
   ```
2. Edit `config.php`:
   - `DB_HOST` / `DB_NAME` / `DB_USER` / `DB_PASS` — your database credentials.
   - `STUDIO_EMAIL` — the address replies are sent *from*.
   - `ADMIN_PASSWORD_HASH` — replace the demo hash. Generate your own with:
     ```
     php -r "echo password_hash('your-new-password', PASSWORD_DEFAULT);"
     ```
     and paste the output in as the constant value.
3. Place this whole `api/` folder alongside `index.html` and `admin.html`
   on a PHP-enabled web server (Apache/Nginx + PHP-FPM, or `php -S`
   for local testing). Both front-end files call the API with relative
   paths like `api/create_order.php`, so keep the folder structure intact.

## Email sending

`reply_message.php` uses PHP's built-in `mail()` function, which relies
on the server having a working local MTA (e.g. `sendmail` or `postfix`)
or a configured SMTP relay in `php.ini`. On most shared hosting this
works out of the box; on a fresh VPS you'll likely need to install and
configure one (or point `sendmail_path` in `php.ini` at an SMTP relay
service). If `mail()` returns `false`, the admin dashboard will show an
error rather than silently failing.

## Security notes (read before going live)

- `ADMIN_PASSWORD_HASH` — change the default password immediately.
- `corsHeaders()` currently allows any origin (`Access-Control-Allow-Origin: *`).
  Tighten this to your real domain once deployed.
- All admin endpoints (`list_orders.php`, `update_order_status.php`,
  `delete_order.php`, `list_messages.php`, `mark_message_read.php`,
  `delete_message.php`, `reply_message.php`) require an authenticated
  session via `requireAdminAuth()`. `create_order.php` and
  `create_message.php` are intentionally public (called from the
  customer-facing site).
- Consider adding rate limiting to the public endpoints
  (`create_order.php`, `create_message.php`) to reduce spam/abuse,
  and HTTPS everywhere so the session cookie and form data aren't
  sent in the clear.
- This uses PHP's native session cookie for admin auth — make sure
  `session.cookie_secure` and `session.cookie_httponly` are enabled
  in `php.ini` for production.

## Endpoints

| Endpoint                    | Method | Auth  | Purpose                          |
|------------------------------|--------|-------|-----------------------------------|
| `login.php`                 | POST   | —     | Admin login, starts session       |
| `logout.php`                | POST   | —     | Destroys admin session            |
| `check_session.php`         | GET    | —     | Is the admin currently logged in? |
| `create_order.php`          | POST   | —     | Save a new booking                |
| `create_message.php`        | POST   | —     | Save a contact form message       |
| `list_orders.php`           | GET    | admin | List/search/filter orders         |
| `update_order_status.php`   | POST   | admin | Change an order's status          |
| `delete_order.php`          | POST   | admin | Delete an order                   |
| `list_messages.php`         | GET    | admin | List/filter messages              |
| `mark_message_read.php`     | POST   | admin | Mark a message read                |
| `delete_message.php`        | POST   | admin | Delete a message                  |
| `reply_message.php`         | POST   | admin | Email a reply using the template  |
