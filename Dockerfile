# Use official PHP 8.2 with extensions
FROM php:8.4-fpm

# Arguments
ARG USER=www-data
ARG UID=1000
ARG GID=1000

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath sockets

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project
COPY . .

COPY .env .env
# Install PHP deps
RUN composer install

# Set ownership and permissions
RUN mkdir -p storage/framework/{views,cache,sessions,testing} storage/logs bootstrap/cache

RUN chown -R ${USER}:${USER} storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache
# Install npm deps (for Vue frontend)
RUN apt-get install -y nodejs npm
RUN npm install
RUN npm run build

# Expose port 9001
EXPOSE 9001

CMD ["php-fpm"]
