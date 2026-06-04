# Docker Deployment Guide

## Local Development with Docker

### Quick Start

1. **Clone the repository:**

    ```bash
    git clone <your-repo>
    cd qr-restaurant
    ```

2. **Build and start containers:**

    ```bash
    docker-compose up -d
    ```

3. **Run migrations:**

    ```bash
    docker-compose exec app php artisan migrate --force
    ```

4. **Access the application:**
    - Open http://localhost in your browser

### Individual Commands

```bash
# Build images
docker-compose build

# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f app

# Run artisan commands
docker-compose exec app php artisan <command>

# Run PHP
docker-compose exec app php -v

# Access MySQL
docker-compose exec db mysql -u root -p qrCode
```

---

## Production Deployment Architecture

### Docker Image Structure

The Dockerfile uses a **multi-stage build** for optimal image size:

```
Stage 1: Node Builder
├── Installs npm dependencies
└── Builds frontend assets (Vite + Tailwind)

Stage 2: PHP Production
├── Installs PHP 8.3-FPM
├── Adds system dependencies
├── Installs PHP extensions
├── Installs Composer dependencies
├── Copies built frontend assets
└── Configures Nginx + Supervisor
```

### Services Included

1. **PHP-FPM** - PHP application runner
2. **Nginx** - Web server
3. **Supervisor** - Process manager (runs PHP-FPM, Nginx, Queue)
4. **Laravel Queue** - Background job processor

### Environment Variables

Create a `.env` file with these critical variables:

```env
# Application
APP_NAME="QR Restaurant"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=qrCode
DB_USERNAME=your_user
DB_PASSWORD=your_password

# Cache & Session
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

---

## Building the Docker Image

### Manual Build

```bash
# Build with tag
docker build -t qr-restaurant:latest .

# Build and push to registry
docker tag qr-restaurant:latest your-registry/qr-restaurant:latest
docker push your-registry/qr-restaurant:latest
```

### Image Size Optimization

Current optimizations in Dockerfile:

- ✅ Multi-stage build
- ✅ Alpine Linux base (minimal size)
- ✅ No dev dependencies in production
- ✅ Removed unnecessary files via .dockerignore
- ✅ Optimized Composer (--no-dev flag)

---

## Nginx Configuration

The included nginx.conf provides:

- ✅ Gzip compression
- ✅ Security headers
- ✅ Cache control for assets
- ✅ URL rewriting for Laravel
- ✅ PHP-FPM integration
- ✅ Static file serving

---

## Running Migrations & Setup

### In Docker Container

```bash
# Run all pending migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
```

### On Railway

See [RAILWAY.md](../RAILWAY.md) for Railway-specific deployment instructions.

---

## Monitoring & Logs

### Check Container Logs

```bash
# All services
docker-compose logs

# Specific service
docker-compose logs app
docker-compose logs db

# Follow logs in real-time
docker-compose logs -f app

# Last 100 lines
docker-compose logs --tail=100 app
```

### Inside Container

```bash
# Check Laravel logs
docker-compose exec app tail -f storage/logs/laravel.log

# Check Nginx logs
docker-compose exec app tail -f /var/log/nginx/error.log

# Check Supervisor
docker-compose exec app supervisorctl status
```

---

## Scaling & Performance

### Increase PHP Workers

Edit `supervisord.conf`:

```conf
[program:laravel-queue]
numprocs = 4  # Increase this
```

### Enable Redis Caching

Add to `docker-compose.yml`:

```yaml
redis:
    image: redis:7-alpine
    container_name: qr-restaurant-redis
    ports:
        - "6379:6379"
    networks:
        - qr-network
```

Then update `.env`:

```
CACHE_STORE=redis
REDIS_HOST=redis
```

---

## Troubleshooting

### Permission Issues

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed

```bash
# Test connection
docker-compose exec app php artisan tinker
>>> DB::connection()->getPdo()
```

### Assets Not Loading

```bash
docker-compose rebuild
docker-compose up -d
```

### Container Won't Start

```bash
docker-compose up app  # See errors without -d
docker-compose logs app
```

---

## Security Best Practices

✅ Already implemented in Dockerfile:

- Non-root user execution
- Alpine Linux (minimal attack surface)
- Latest PHP 8.3 security patches
- Security headers in Nginx

Additional recommendations:

- Use environment variables for secrets
- Enable HTTPS/TLS
- Keep Docker images updated
- Use private Docker registries
- Regularly scan images for vulnerabilities

```bash
# Scan with Trivy
trivy image qr-restaurant:latest
```

---

## Cleanup

```bash
# Stop and remove containers
docker-compose down

# Remove volumes
docker-compose down -v

# Remove images
docker rmi qr-restaurant:latest

# Remove all unused Docker resources
docker system prune -a
```

---

## Additional Resources

- [Laravel Docker](https://laravel.com/docs/deployment)
- [Nginx Configuration](https://nginx.org/en/docs/)
- [Docker Best Practices](https://docs.docker.com/develop/dev-best-practices/)
- [Supervisor](http://supervisord.org/)
