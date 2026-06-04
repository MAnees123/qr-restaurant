# Railway Deployment Guide for QR Restaurant

## Overview

This guide provides step-by-step instructions to deploy your Laravel QR Restaurant application on Railway.app with Docker.

---

## Prerequisites

1. Railway.app account (sign up at https://railway.app)
2. GitHub repository with your code pushed
3. Docker configured locally (for local testing)

---

## Step 1: Prepare Your Repository

1. **Create `.env.railway`** (Railway-specific environment file):

    ```bash
    touch .env.railway
    ```

2. **Add the following to `.env.railway`:**

    ```env
    APP_NAME="QR Restaurant"
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=${RAILWAY_PUBLIC_DOMAIN}

    DB_CONNECTION=mysql
    DB_HOST=${DB_HOST}
    DB_PORT=${DB_PORT}
    DB_DATABASE=${DB_DATABASE}
    DB_USERNAME=${DB_DATABASE_USER}
    DB_PASSWORD=${DB_DATABASE_PASSWORD}

    LOG_CHANNEL=single
    LOG_LEVEL=info

    SESSION_DRIVER=database
    CACHE_STORE=database
    QUEUE_CONNECTION=database

    MAIL_MAILER=log

    BROADCAST_CONNECTION=log
    FILESYSTEM_DISK=public
    ```

3. **Generate a new APP_KEY for production:**

    ```bash
    php artisan key:generate --show
    ```

    Copy the output and save it (you'll need this for Railway)

4. **Push to GitHub:**
    ```bash
    git add .
    git commit -m "Add Docker configuration for Railway"
    git push origin main
    ```

---

## Step 2: Create Railway Project

1. Go to [Railway Dashboard](https://railway.app/dashboard)
2. Click **"+ New Project"**
3. Select **"Deploy from GitHub repo"**
4. Authorize Railway with GitHub
5. Select your repository
6. Click **"Deploy Now"**

---

## Step 3: Configure Database

Railway will automatically detect the Dockerfile. Now add a MySQL database:

1. In Railway Dashboard, click **"+ Add Service"**
2. Select **"MySQL"**
3. Confirm by clicking **"Create"**

The MySQL service is now added to your project.

---

## Step 4: Configure Environment Variables

1. Click on the **"web"** service (your app)
2. Go to the **"Variables"** tab
3. Add the following variables:

    | Variable           | Value                                |
    | ------------------ | ------------------------------------ |
    | `APP_KEY`          | Paste the key from Step 1            |
    | `APP_ENV`          | production                           |
    | `APP_DEBUG`        | false                                |
    | `APP_URL`          | Leave empty (Railway auto-generates) |
    | `LOG_CHANNEL`      | single                               |
    | `QUEUE_CONNECTION` | database                             |
    | `SESSION_DRIVER`   | database                             |
    | `CACHE_STORE`      | database                             |

4. **For Database Connection:**
    - Go to the **"MySQL"** service
    - Click **"Data"** tab to see credentials
    - In **web service variables**, click **"Reference Variables"**
    - Add these variables (they auto-populate from MySQL):
        - `DB_HOST` → `${{ mysql.MYSQL_HOST }}`
        - `DB_PORT` → `${{ mysql.MYSQL_PORT }}`
        - `DB_USERNAME` → `${{ mysql.MYSQL_USER }}`
        - `DB_PASSWORD` → `${{ mysql.MYSQL_PASSWORD }}`
        - `DB_DATABASE` → `${{ mysql.MYSQL_DATABASE }}`

---

## Step 5: Run Database Migrations

Once deployment completes:

1. Click the **"web"** service
2. Go to the **"Deploy"** tab
3. Click the **"View Logs"** button to monitor deployment

To run migrations manually:

1. Get the public URL (visible in the Deployments tab)
2. Use Railway CLI (optional):
    ```bash
    npm install -g @railway/cli
    railway link
    railway run php artisan migrate --force
    ```

**Or** add a one-time initialization hook in Dockerfile:
Update the `Dockerfile` to include migrations in the startup process (already included in supervisord configuration).

---

## Step 6: Verify Deployment

1. Click the **public URL** provided by Railway
2. Your QR Restaurant app should be live!
3. Check logs in Railway Dashboard if there are issues

---

## Common Issues & Solutions

### Issue 1: Database Connection Fails

**Solution:**

- Verify Database connection variables are correctly referenced
- Check MySQL service is running (should see green status)
- Ensure firewall allows Railway's IP

### Issue 2: 500 Internal Server Error

**Solution:**

```bash
# SSH into Railway container and check logs:
railway run tail -f storage/logs/laravel.log

# Or clear cache:
railway run php artisan cache:clear
railway run php artisan config:clear
```

### Issue 3: Assets Not Loading (CSS/JS)

**Solution:**

```bash
# Rebuild assets:
railway run npm run build

# Or re-deploy to trigger full rebuild
```

### Issue 4: Storage Permissions Error

**Solution:**
The Dockerfile already handles this, but if issues persist:

```bash
railway run chmod -R 775 storage bootstrap/cache
```

---

## Step 7: Configure Custom Domain (Optional)

1. In Railway Dashboard, go to your **web** service
2. Click **"Settings"**
3. Under **"Domain"**, add your custom domain
4. Update DNS records at your domain provider to point to Railway

---

## Step 8: SSL/HTTPS (Automatic)

Railway automatically provides SSL certificates for:

- `*.railway.app` domains
- Custom domains (auto-renewed)

No additional configuration needed!

---

## Useful Railway CLI Commands

```bash
# Install CLI
npm install -g @railway/cli

# Login
railway login

# Link project
railway link

# Run commands in production
railway run php artisan migrate
railway run php artisan tinker
railway run tail -f storage/logs/laravel.log

# Deploy
railway up

# View logs
railway logs
```

---

## Monitoring & Logs

1. **Real-time Logs:**
    - Railway Dashboard → service → "Logs" tab

2. **Application Logs:**

    ```bash
    railway run tail -f storage/logs/laravel.log
    ```

3. **Database Logs:**
    - Railway Dashboard → MySQL service → "Logs" tab

---

## Cost Optimization

Railway charging is based on resource usage:

1. **Free Tier:**
    - Up to $5/month included monthly
    - Good for small projects

2. **Optimization Tips:**
    - Use smaller MySQL instances if possible
    - Monitor memory usage
    - Enable auto-scaling on the web service
    - Use Railway's built-in caching

---

## Backup & Recovery

1. **Database Backups:**
    - Railway automatically backs up MySQL
    - Access via Railway Dashboard

2. **Manual Backup:**
    ```bash
    railway run php artisan backup:run
    ```

---

## Next Steps

- Set up CI/CD pipeline for automatic deployments
- Configure error monitoring (Sentry, etc.)
- Add Redis for caching (optional)
- Set up email service for production

For more information, visit [Railway Docs](https://docs.railway.app)
