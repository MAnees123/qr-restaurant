# QR Restaurant - Quick Start Guide

## 🚀 Local Development Setup

### Prerequisites

- Docker & Docker Compose installed
- Git
- 4GB+ RAM available

### 1. Copy Environment File

```bash
cp .env.example .env
```

### 2. Generate APP_KEY

```bash
php artisan key:generate
# Or inside Docker:
docker-compose exec app php artisan key:generate
```

### 3. Start Docker Services

```bash
docker-compose up -d
```

### 4. Install & Migrate

```bash
docker-compose exec app composer install
docker-compose exec app npm install
docker-compose exec app php artisan migrate --force
docker-compose exec app npm run build
```

### 5. Access Application

```
http://localhost
```

---

## 📦 Production Deployment - Railway

See [RAILWAY.md](./RAILWAY.md) for detailed instructions.

**Quick steps:**

1. Push code to GitHub
2. Connect Railway to GitHub repo
3. Add MySQL service
4. Configure environment variables
5. Deploy!

---

## 🐳 Docker Commands Reference

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f app

# Run artisan command
docker-compose exec app php artisan migrate

# Run npm build
docker-compose exec app npm run build

# Access shell
docker-compose exec app sh
```

---

## 📋 Project Structure

```
qr-restaurant/
├── app/              # Laravel application code
├── database/         # Migrations, seeders
├── resources/        # Views, CSS, JS
├── routes/           # API & web routes
├── storage/          # Logs, uploads
├── docker/           # Docker configuration files
├── Dockerfile        # Production Docker image
├── docker-compose.yml # Local development setup
├── DOCKER.md         # Docker guide
└── RAILWAY.md        # Railway deployment guide
```

---

## 🔧 Environment Variables

Key environment variables to configure:

| Variable      | Local     | Production      |
| ------------- | --------- | --------------- |
| `APP_ENV`     | local     | production      |
| `APP_DEBUG`   | true      | false           |
| `DB_HOST`     | 127.0.0.1 | db service host |
| `LOG_CHANNEL` | stack     | single          |
| `CACHE_STORE` | file      | database/redis  |

---

## 🛠️ Available Commands

```bash
# View all available artisan commands
docker-compose exec app php artisan

# Key commands:
docker-compose exec app php artisan migrate        # Run migrations
docker-compose exec app php artisan seed          # Seed database
docker-compose exec app php artisan tinker         # Laravel REPL
docker-compose exec app php artisan queue:work    # Start queue worker
docker-compose exec app php artisan cache:clear   # Clear cache
```

---

## 📋 Database

### Local MySQL Access

```bash
docker-compose exec db mysql -u root -p qrCode
```

### Create Test Data

```bash
docker-compose exec app php artisan seed
```

---

## 🐛 Troubleshooting

### Containers won't start

```bash
docker-compose up app  # See full error output
```

### Database connection error

```bash
# Check MySQL is running
docker-compose ps

# Restart services
docker-compose restart
```

### Permission denied errors

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Need to rebuild

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Docker Documentation](https://docs.docker.com)
- [Railway Documentation](https://docs.railway.app)
- [Nginx Configuration](https://nginx.org/en/docs/)

---

## 🚢 Deployment Checklist

Before deploying to production:

- [ ] Update `.env` with production values
- [ ] Generate new APP_KEY for production
- [ ] Run database migrations
- [ ] Build frontend assets
- [ ] Test locally with `docker-compose`
- [ ] Configure Railway environment variables
- [ ] Enable HTTPS/TLS
- [ ] Set up backups
- [ ] Configure monitoring & logs
- [ ] Test all features on staging

---

**Questions? Check DOCKER.md or RAILWAY.md for detailed guides!**
