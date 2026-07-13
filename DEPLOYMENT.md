# Deploying Composer Dependencies Without Composer on the Target Server

**Use this when:** the deployment target (cPanel shared hosting, an offline/locked-down
Ubuntu server, or any server without reliable outbound internet access) does not have
Composer installed and you can't or don't want to install it there.

**Core idea:** Composer only needs to run somewhere you have full control and internet
access (your local Ubuntu dev machine). You build the `vendor/` folder there, zip it,
and ship the finished folder to the server. The server never talks to Packagist — it
just needs the files to exist.

---

## 0. Where to keep this file

Put this file at the root of each project as `DEPLOYMENT.md`, or in a shared
`docs/deployment/` folder if you want one copy referenced across JCIMS, Warka Hub HRMS,
EAIMS, etc. Suggested repo layout:

```
JCIMS/
├── DEPLOYMENT.md          <- this file
├── composer.json
├── composer.lock
├── src/
├── public/
└── vendor/                <- gitignored, rebuilt/deployed per environment
```

Keep `vendor/` in `.gitignore` as usual — this workflow deploys it out-of-band, it
doesn't belong in git history.

---

## 1. Prerequisites (one-time, on your local machine)

Check Composer is installed locally:
```bash
composer --version
```

If missing, install once:
```bash
curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php --install-dir=$HOME/bin --filename=composer
echo 'export PATH=$HOME/bin:$PATH' >> ~/.bashrc
source ~/.bashrc
composer --version
```

Confirm your local PHP CLI version roughly matches the server's PHP version (check
`platform.php` in `composer.json` — for JCIMS this is pinned to `8.2.12`):
```bash
php -v
```

---

## 2. Build the vendor package locally

From the project root:
```bash
cd /var/www/html/JCIMS        # or whichever project
composer install --no-dev --optimize-autoloader
```

- `--no-dev` — skips dev-only dependencies (keeps the deploy lean)
- `--optimize-autoloader` — generates a classmap, faster autoloading in production;
  matches the `"optimize-autoloader": true` already set in `composer.json`

Verify the expected packages landed:
```bash
ls vendor/ramsey vendor/vlucas vendor/monolog vendor/phpoffice
```

Package it up, always including `composer.lock` (it's the exact record of resolved
versions — keeps every environment identical):
```bash
zip -r vendor-deploy.zip vendor/ composer.json composer.lock
```

Check the size — `phpoffice/phpspreadsheet` pulls in several sub-dependencies
(`psr/*`, `markbaker/*`), so expect tens of MB. Normal.

---

## 3. Transfer to the server

### 3a. cPanel shared hosting (e.g. `acapulco` / `ltbdevmy`)

If you have SSH (as with `ltbdevmy@acapulco`), prefer `scp` over File Manager upload:
```bash
scp vendor-deploy.zip ltbdevmy@acapulco:~/path/to/JCIMS/
```

Then SSH in and extract:
```bash
ssh ltbdevmy@acapulco
cd ~/path/to/JCIMS
unzip vendor-deploy.zip
```

If you don't have SSH (File Manager only):
1. Upload `vendor-deploy.zip` via cPanel **File Manager** into the app root.
2. Select it and use the built-in **Extract** feature (far more reliable than
   uploading thousands of individual small vendor files over FTP/SFTP).
3. Delete the zip afterward to save space/avoid it being web-accessible.

### 3b. Your own Ubuntu server (VPS / TeleCloud / anything you have root on)

If the server *has* internet access, it's usually simpler to just install Composer
there directly and run `composer install` on the server itself — skip this whole
workflow. Only use the zip-and-ship method here if:
- The server has no outbound internet access (common on internal government networks)
- You want reproducible deploys — same exact `vendor/` across staging and production
- You're deploying to multiple servers and don't want to hit Packagist from each

If so:
```bash
scp vendor-deploy.zip user@server:/var/www/html/JCIMS/
ssh user@server
cd /var/www/html/JCIMS
unzip vendor-deploy.zip
```

`rsync` is a good alternative to `scp` for repeat deploys (only transfers diffs):
```bash
rsync -avz vendor/ user@server:/var/www/html/JCIMS/vendor/
```

---

## 4. Verify on the server

```bash
ls vendor/autoload.php
```
If this file exists, your bootstrap's
`require __DIR__ . '/../vendor/autoload.php';` will work with no further steps.

Optional sanity check — confirm PHP can actually load it without errors:
```bash
php -r "require 'vendor/autoload.php'; echo 'OK';"
```

---

## 5. ionCube encoding — order of operations

If encoding `App\` source for a client-owned server deployment:
- Run `composer install` **first**, encode **after**.
- **Never encode `vendor/`** — third-party packages (`ramsey/uuid`, `monolog`,
  `phpdotenv`, `phpspreadsheet`) are not your IP and encoding them is unnecessary and
  can break autoloading. Only encode `src/` (Models/Controllers), not `vendor/` or
  `views/`.

---

## 6. Security note for shared cPanel hosting

Keep `vendor/`, `composer.json`, and `composer.lock` **outside** `public_html` if your
hosting structure allows a document root separate from the account home — same
reasoning as keeping `.sql` files out of `public_html`. Only `public/index.php` (or
equivalent front controller) should be web-exposed.

---

## 7. Quick checklist for future deploys

- [ ] `composer install --no-dev --optimize-autoloader` locally
- [ ] Confirm `vendor/<expected-packages>` exist
- [ ] `zip -r vendor-deploy.zip vendor/ composer.json composer.lock`
- [ ] Transfer via `scp`/`rsync` (SSH available) or File Manager upload + Extract
      (SSH unavailable)
- [ ] Confirm `vendor/autoload.php` exists on server
- [ ] `php -r "require 'vendor/autoload.php'; echo 'OK';"` sanity check
- [ ] If ionCube encoding: encode `src/` only, after this process, never `vendor/`
- [ ] Confirm `vendor/`, `composer.json`, `composer.lock` sit outside the public web
      root