# 1. Start with a lightweight PHP image
FROM php:8.2-cli

# 2. Install the necessary system dependencies and PHP extensions for Laravel
RUN apt-get update && apt-get install -y unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# 3. Copy Composer from the official source
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set the working directory inside the container
WORKDIR /app

# 5. Copy your entire project into the container
COPY . .

# 6. Install your Laravel dependencies safely
RUN composer install --no-dev --optimize-autoloader

# 7. Migrate the TiDB database and boot the built-in Laravel server on the port Render assigns
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

# Make the script executable
RUN chmod +x /var/www/html/start.sh

# Run the script when the container launches
CMD ["/var/www/html/start.sh"]
