# 1) Сборка приложения и установка зависимостей Composer
FROM php:8.2-cli AS builder

RUN apt-get update \
 && apt-get install -y \
    git zip unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql mbstring zip exif pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Ставим зависимости, но не выполняем скрипты (там artisan ещё нет)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Копируем весь код
COPY . .

# Генерируем автозагрузку и делаем discovery пакетов
RUN composer dump-autoload --optimize \
 && php artisan package:discover --ansi

# 2) Финальный образ с уже готовым кодом и нужными расширениями
FROM php:8.2-cli

# Устанавливаем системные пакеты и те же PHP‑расширения
RUN apt-get update \
 && apt-get install -y \
    libzip-dev libpng-dev libonig-dev libxml2-dev \
 && docker-php-ext-install pdo_mysql mbstring zip exif pcntl \
 && rm -rf /var/lib/apt/lists/*

# Копируем код и vendor из builder
COPY --from=builder /var/www/html /var/www/html

WORKDIR /var/www/html

# Запускаем встроенный сервер Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
