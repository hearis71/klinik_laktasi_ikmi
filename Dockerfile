FROM php:8.1-apache

# Set default PORT environment variable
ENV PORT=8080
ENV APACHE_DOCUMENT_ROOT=/var/www/html

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache modules
RUN a2dismod mpm_event mpm_worker || true
RUN a2enmod mpm_prefork rewrite ssl

# Configure Apache document root and port
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

RUN sed -ri 's/80/${PORT}/g' /etc/apache2/ports.conf \
 && sed -ri 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/html/

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080

CMD ["apache2-foreground"]