# PHP 8.1 with Apache
FROM php:8.1-apache

# install extensions
RUN docker-php-ext-install pdo pdo_mysql

# copy application source
COPY . /var/www/html/

# set permissions (optional but safe)
RUN chown -R www-data:www-data /var/www/html

# enable apache rewrite if needed
RUN a2enmod rewrite

# expose port 80 for HTTP
EXPOSE 80

# default command is provided by base image (apache2-foreground)
