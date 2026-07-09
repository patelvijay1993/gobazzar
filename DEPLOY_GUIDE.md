# GoBazaar — Shared Hosting Deploy Guide
# Domain: https://gobazzarweb.heavendwell.com/

## Step 1 — cPanel MySQL Database

1. cPanel login → MySQL Databases
2. Create database: e.g. `heavend_gobazzar`
3. Create user + password
4. Add user to DB with ALL PRIVILEGES
5. Note: DB_HOST (usually `localhost`), DB_DATABASE, DB_USERNAME, DB_PASSWORD

---

## Step 2 — .env file update (on server)

After uploading, edit `.env` file on server with these values:

```
APP_NAME=GoBazaar
APP_ENV=production
APP_KEY=          ← same as local (copy from local .env)
APP_DEBUG=false
APP_URL=https://gobazzarweb.heavendwell.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=heavend_gobazzar   ← your actual DB name
DB_USERNAME=heavend_gobazzaruser  ← your actual DB user
DB_PASSWORD=YourStrongPassword    ← your actual DB password

FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=smtp.resend.com
MAIL_PORT=465
MAIL_SCHEME=smtps
MAIL_USERNAME=resend
MAIL_PASSWORD=       ← copy from local .env
MAIL_FROM_ADDRESS=noreply@gobazzarweb.heavendwell.com
MAIL_FROM_NAME="GoBazaar"

AWS_ACCESS_KEY_ID=       ← copy from local .env
AWS_SECRET_ACCESS_KEY=   ← copy from local .env
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=gobaazar
AWS_USE_PATH_STYLE_ENDPOINT=false

STRIPE_KEY=        ← copy from local .env
STRIPE_SECRET=     ← copy from local .env
STRIPE_WEBHOOK_SECRET=  ← copy from local .env

REQUIRE_EMAIL_VERIFICATION=false
```

---

## Step 3 — Files Upload via cPanel File Manager / FTP

### Option A: GitHub se pull (Recommended if hosting has SSH/Git)
```bash
git clone https://github.com/patelvijay1993/gobazzar.git
```

### Option B: ZIP upload
1. Local ma zip banavo (vendor folder baad karo — bahu motu hoy)
2. cPanel File Manager ma upload karo
3. Extract karo

### Important folder structure:
```
public_html/          ← sirf public/ folder nu content aave
  index.php
  .htaccess
  images/
  favicon.png
  css/, js/ etc.

gobazzar/             ← root folder (public_html bahar)
  app/
  bootstrap/
  config/
  database/
  resources/
  routes/
  storage/
  vendor/
  .env
  artisan
  composer.json
```

---

## Step 4 — public/index.php path fix

`public/index.php` ma paths update karo:

```php
// Change these two lines:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// To point to your actual folder location:
require __DIR__.'/../../gobazzar/vendor/autoload.php';
$app = require_once __DIR__.'/../../gobazzar/bootstrap/app.php';
```

---

## Step 5 — Artisan commands (via cPanel Terminal or SSH)

```bash
cd /home/username/gobazzar
php artisan key:generate        # if APP_KEY empty
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 6 — Storage permissions

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

---

## Step 7 — PHP version check

cPanel → MultiPHP Manager → set PHP 8.2 or 8.3 for your domain

---

## Common Issues

| Issue | Fix |
|-------|-----|
| 500 Error | Check storage/ permissions, check .env APP_KEY |
| Images not showing | Run `php artisan storage:link` |
| DB connection error | Check DB credentials in .env |
| Filament 404 | Check APP_URL in .env matches domain exactly |
