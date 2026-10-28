# MES Deployment Guide for Ubuntu Server

## Prerequisites
- Ubuntu 22.04 LTS or higher
- PostgreSQL 14+
- PHP 8.1+
- Composer
- Node.js 18+
- Nginx
- Supervisor

---

## 1. Install System Dependencies

```bash
sudo apt update
sudo apt install -y nginx postgresql postgresql-contrib php8.1-fpm php8.1-pgsql \
    php8.1-mbstring php8.1-xml php8.1-curl php8.1-zip php8.1-bcmath php8.1-gd \
    php8.1-redis supervisor git unzip curl
```

---

## 2. Configure PostgreSQL

```bash
sudo -u postgres psql
CREATE DATABASE mes_manufacturing;
CREATE USER mes_user WITH PASSWORD 'your_secure_password';
GRANT ALL PRIVILEGES ON DATABASE mes_manufacturing TO mes_user;
\c mes_manufacturing
GRANT ALL ON SCHEMA public TO mes_user;
\q
```

---

## 3. Deploy Application

```bash
# Clone or upload application
cd /var/www
sudo mkdir -p mes
sudo chown $USER:$USER mes
cd mes

# Clone your repository
git clone <repository_url> .

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/mes
sudo chmod -R 755 /var/www/mes/storage /var/www/mes/bootstrap/cache
```

---

## 4. Configure Environment

```bash
cp .env.example .env
nano .env
```

Update the following:
```
APP_NAME="MES - Manufacturing Execution System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mes.yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=mes_manufacturing
DB_USERNAME=mes_user
DB_PASSWORD=your_secure_password
INITIAL_ADMIN_PASSWORD=

QUEUE_CONNECTION=database
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=your_redis_password
REDIS_PORT=6379
```

Set `INITIAL_ADMIN_PASSWORD` to a unique password before running the database seeder.

Generate application key:
```bash
php artisan key:generate
```

---

## 5. Database Migration & Seeding

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

---

## 6. Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/mes
```

```nginx
server {
    listen 80;
    server_name mes.yourdomain.com;
    root /var/www/mes/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
sudo ln -s /etc/nginx/sites-available/mes /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

---

## 7. Setup Queue Worker

```bash
sudo nano /etc/supervisor/conf.d/mes-worker.conf
```

```ini
[program:mes-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/mes/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasuser=false
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/mes/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mes-worker:*
```

---

## 8. Setup Scheduler (Cron)

```bash
crontab -e
```

Add:
```
* * * * * cd /var/www/mes && php artisan schedule:run >> /dev/null 2>&1
```

---

## 9. SSL Certificate (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d mes.yourdomain.com
```

---

## 10. PostgreSQL Optimization

Edit `/etc/postgresql/14/main/postgresql.conf`:

```ini
shared_buffers = 256MB
effective_cache_size = 1GB
maintenance_work_mem = 64MB
work_mem = 4MB
random_page_cost = 1.1
effective_io_concurrency = 200
max_worker_processes = 4
max_parallel_workers_per_gather = 2
max_parallel_workers = 4
```

Restart PostgreSQL:
```bash
sudo systemctl restart postgresql
```

---

## 11. Security Best Practices

1. **File Permissions:** Only `www-data` owns storage and bootstrap/cache
2. **APP_DEBUG=false** in production
3. **HTTPS only** - redirect HTTP to HTTPS
4. **Rate limiting** on API endpoints
5. **Regular backups:**
   ```bash
   pg_dump mes_manufacturing > /backup/mes_$(date +%Y%m%d).sql
   ```
6. **Keep packages updated:** `composer update --no-dev`
7. **Use firewall:** `ufw allow 80,443,22`

---

## 12. Monitoring

```bash
# Check queue workers
sudo supervisorctl status

# View application logs
tail -f /var/www/mes/storage/logs/laravel.log

# View worker logs
tail -f /var/www/mes/storage/logs/worker.log

# Check database connections
sudo -u postgres psql -c "SELECT count(*) FROM pg_stat_activity;"
```
