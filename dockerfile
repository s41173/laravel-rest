# Base image
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Copy environment file
COPY .env.example .env

# Generate app key
RUN php artisan key:generate

# Expose port
EXPOSE 9000

# Run migrations automatically (optional)
# RUN php artisan migrate --force

# Start PHP-FPM
CMD ["php-fpm"]