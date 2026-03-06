FROM php:8.2-apache-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy project
COPY . .

# Set permission
RUN chown -R www-data:www-data /var/www/html

# Expose port
EXPOSE 80

CMD ["apache2-foreground"]