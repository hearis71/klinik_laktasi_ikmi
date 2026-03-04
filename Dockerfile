# PHP 8.1 with Apache
FROM php:8.1-apache

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source
COPY . /var/www/html/

# Install dependencies (if composer.json exists)
RUN if [ -f composer.json ]; then composer install --no-dev --optimize-autoloader --no-interaction; fi

# Set permissions (optional but safe)
RUN chown -R www-data:www-data /var/www/html

# Enable apache rewrite if needed
RUN a2enmod rewrite

# Expose port 80 for HTTP
EXPOSE 80

# Default command is provided by base image (apache2-foreground)
