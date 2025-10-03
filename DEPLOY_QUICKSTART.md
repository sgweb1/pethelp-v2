# ⚡ Quick Start - Deployment na Cyberfolk

## 🎯 Szybki deployment w 3 krokach

### 1️⃣ Połącz się z serwerem

```bash
ssh twoj-user@pethelp.pro-linuxpl.com
```

### 2️⃣ Przejdź do aplikacji

```bash
cd ~/pethelp
```

### 3️⃣ Uruchom deployment

```bash
./deploy.sh
```

**Gotowe!** ✅

---

## 📋 Pierwsza instalacja skryptu

Jeśli `deploy.sh` nie istnieje na serwerze:

```bash
# Pobierz najnowsze zmiany
cd ~/pethelp
git pull origin master

# Nadaj uprawnienia
chmod +x deploy.sh

# Uruchom
./deploy.sh
```

---

## 🔧 Ręczne deployment (bez skryptu)

```bash
cd ~/pethelp
php artisan down
git pull origin master
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

---

## 🆘 W razie problemów

```bash
# Sprawdź logi
tail -100 ~/pethelp/storage/logs/laravel.log

# Wyczyść cache
php artisan optimize:clear

# Wyłącz maintenance mode
php artisan up
```

---

## 📚 Pełna dokumentacja

- **Szczegółowy deployment**: [DEPLOYMENT.md](DEPLOYMENT.md)
- **Pierwsza instalacja**: [HOSTING_SHARED_INSTALL.md](HOSTING_SHARED_INSTALL.md)

---

## ✅ Sprawdź czy działa

Po deployment:

- 🌐 Strona: https://pethelp.pro-linuxpl.com
- 🛡️ Admin: https://pethelp.pro-linuxpl.com/admin
- 👤 Profil: https://pethelp.pro-linuxpl.com/profil

---

**Pytania?** Sprawdź pełną dokumentację w [DEPLOYMENT.md](DEPLOYMENT.md)
