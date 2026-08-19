#!/usr/bin/env python3
"""Deploy DamioRif to OVH VPS — same method as Yallah-Go / BeauMiel / SpeedyPrint."""
from __future__ import annotations

import os
import secrets
import sys
import tarfile
import tempfile
import time
from pathlib import Path

import paramiko

sys.stdout.reconfigure(encoding="utf-8", errors="replace")

HOST = "51.255.162.99"
USER = "ubuntu"
PW = os.environ.get("VPS_SSH_PASSWORD", "A2sprVps2026!Secure")
FQDN = "damiorif.a2spr.com"
SLUG = "damiorif"
ROOT = f"/var/www/{FQDN}"
APP = f"{ROOT}/app"
DB_NAME = "damiorif"
DB_USER = "damiorif"
LOCAL = Path(__file__).resolve().parent

EXCLUDE_DIRS = {
    ".git",
    "node_modules",
    "vendor",
    ".cursor",
    "storage/logs",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
}
EXCLUDE_FILES = {
    ".env",
    "deploy.py",
    "deploy.bat",
    "serve.ps1",
}
EXCLUDE_PREFIXES = ("_tmp_",)


def should_exclude(rel: Path) -> bool:
    rel_posix = str(rel).replace("\\", "/")
    for d in EXCLUDE_DIRS:
        if rel_posix == d or rel_posix.startswith(d + "/"):
            return True
    if rel.name in EXCLUDE_FILES:
        return True
    if any(rel.name.startswith(p) for p in EXCLUDE_PREFIXES):
        return True
    return False


def safe_print(text: str) -> None:
    try:
        print(text, flush=True)
    except UnicodeEncodeError:
        print(text.encode("ascii", "replace").decode("ascii"), flush=True)


def main() -> None:
    if not PW:
        safe_print("Set VPS_SSH_PASSWORD before running deploy.py")
        sys.exit(1)

    db_pass = secrets.token_urlsafe(18)

    c = paramiko.SSHClient()
    c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    safe_print(f"Connecting to {HOST}...")
    c.connect(
        HOST,
        username=USER,
        password=PW,
        timeout=40,
        allow_agent=False,
        look_for_keys=False,
        banner_timeout=90,
    )

    def run(cmd: str, t: int = 900, check: bool = True) -> tuple[str, int]:
        safe_print(f"\n>>> {cmd[:240]}")
        _, stdout, stderr = c.exec_command(cmd, timeout=t, get_pty=True)
        out = stdout.read().decode("utf-8", "ignore")
        err = stderr.read().decode("utf-8", "ignore")
        code = stdout.channel.recv_exit_status()
        text = (out + err).strip()
        if text:
            lines = text.splitlines()
            if len(lines) > 80:
                text = "\n".join(lines[:40] + ["..."] + lines[-40:])
            safe_print(text)
        if check and code != 0:
            safe_print(f"FAILED exit={code}")
            sys.exit(code)
        return out, code

    run("ls -1 /var/www | head -100", check=False)
    run(f"test -d {ROOT} && echo EXISTS || echo '{PW}' | sudo -S new-site {SLUG} php", check=False)
    run(f"mkdir -p {ROOT} && echo '{PW}' | sudo -S chown -R ubuntu:www-data {ROOT}")

    run(f"cp {APP}/.env /tmp/damiorif_env_backup 2>/dev/null || true", check=False)
    run(f"rm -rf {APP} && mkdir -p {APP}")

    safe_print("\n>>> packing local tree…")
    tar_path = Path(tempfile.gettempdir()) / f"damiorif-deploy-{int(time.time())}.tar.gz"
    with tarfile.open(tar_path, "w:gz") as tar:
        for p in LOCAL.rglob("*"):
            if not p.is_file():
                continue
            rel = p.relative_to(LOCAL)
            if should_exclude(rel):
                continue
            tar.add(p, arcname=str(rel).replace("\\", "/"))
    safe_print(f"    archive: {tar_path} ({tar_path.stat().st_size // 1024} KB)")

    safe_print("\n>>> sftp upload…")
    sftp = c.open_sftp()
    remote_tar = "/tmp/damiorif-deploy.tar.gz"
    sftp.put(str(tar_path), remote_tar)
    sftp.close()
    tar_path.unlink(missing_ok=True)

    run(f"tar -xzf {remote_tar} -C {APP} && rm -f {remote_tar}")
    run(
        f"mkdir -p {APP}/storage/framework/{{cache,sessions,views}} "
        f"{APP}/storage/logs {APP}/bootstrap/cache {APP}/storage/app/public"
    )

    env_check, _ = run(
        "test -f /tmp/damiorif_env_backup && echo HAS_ENV || echo NO_ENV",
        check=False,
    )
    has_env = "HAS_ENV" in env_check

    if has_env:
        run(f"cp /tmp/damiorif_env_backup {APP}/.env")
        safe_print("Restored existing .env")
    else:
        env = f"""APP_NAME="DamioRif"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://{FQDN}

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_FAKER_LOCALE=fr_FR

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE={DB_NAME}
DB_USERNAME={DB_USER}
DB_PASSWORD={db_pass}

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@{FQDN}"
MAIL_FROM_NAME="${{APP_NAME}}"

VITE_APP_NAME="${{APP_NAME}}"
"""
        sql = f"""CREATE DATABASE IF NOT EXISTS {DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '{DB_USER}'@'localhost' IDENTIFIED BY '{db_pass}';
ALTER USER '{DB_USER}'@'localhost' IDENTIFIED BY '{db_pass}';
GRANT ALL PRIVILEGES ON {DB_NAME}.* TO '{DB_USER}'@'localhost';
FLUSH PRIVILEGES;
"""
        run(f"cat > /tmp/damiorif.sql <<'EOSQL'\n{sql}\nEOSQL")
        run(f"echo '{PW}' | sudo -S mysql < /tmp/damiorif.sql && rm -f /tmp/damiorif.sql")
        run(f"cat > {APP}/.env <<'EOF'\n{env}\nEOF")

    nginx_conf = f"""server {{
    listen 80;
    listen [::]:80;
    server_name {FQDN};
    root {APP}/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {{
        try_files $uri $uri/ /index.php?$query_string;
    }}

    location = /favicon.ico {{ access_log off; log_not_found off; }}
    location = /robots.txt  {{ access_log off; log_not_found off; }}

    error_page 404 /index.php;

    location ~ \\.php$ {{
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php-fpm.sock;
    }}

    location ~ /\\.(?!well-known).* {{
        deny all;
    }}
}}
"""
    run(
        "python3 - <<'PY'\n"
        f"open('/tmp/{FQDN}.nginx','w').write({nginx_conf!r})\n"
        "PY"
    )
    run(f"echo '{PW}' | sudo -S mv /tmp/{FQDN}.nginx /etc/nginx/sites-available/{FQDN}")
    run(f"echo '{PW}' | sudo -S ln -sfn /etc/nginx/sites-available/{FQDN} /etc/nginx/sites-enabled/{FQDN}")
    run(f"echo '{PW}' | sudo -S nginx -t && echo '{PW}' | sudo -S systemctl reload nginx")

    run(f"cd {APP} && composer install --no-dev --optimize-autoloader --no-interaction", t=1200)
    run(
        f"cd {APP} && if ! grep -q '^APP_KEY=base64:' .env; then php artisan key:generate --force; fi",
        check=False,
    )
    run(f"cd {APP} && npm ci --ignore-scripts && npm run build", t=1200, check=False)
    run(f"cd {APP} && php artisan storage:link || true", check=False)
    run(f"cd {APP} && php artisan migrate --force")
    run(f"cd {APP} && php artisan db:seed --force", check=False)
    run(
        f"cd {APP} && php artisan route:clear && php artisan config:clear "
        f"&& php artisan view:clear && php artisan config:cache && php artisan view:cache",
        check=False,
    )
    run(
        f"echo '{PW}' | sudo -S chown -R ubuntu:www-data {APP} && "
        f"echo '{PW}' | sudo -S chmod -R ug+rwx {APP}/storage {APP}/bootstrap/cache"
    )
    run(
        f"echo '{PW}' | sudo -S certbot --nginx -d {FQDN} --non-interactive "
        f"--agree-tos -m admin@a2spr.com --redirect || echo CERTBOT_SKIP",
        check=False,
    )

    run(f"curl -sI -H 'Host: {FQDN}' http://127.0.0.1 | head -15", check=False)
    _, status = run(
        f"curl -s -o /dev/null -w '%{{http_code}}' https://{FQDN}/ || true",
        check=False,
    )

    safe_print("\n=== DEPLOY DONE ===")
    safe_print(f"URL: https://{FQDN}")
    safe_print("Login: abdelilah / password")
    safe_print(f"HTTPS probe: {status}")
    c.close()


if __name__ == "__main__":
    main()
