FROM php:8.0-apache

COPY . /var/www/html/

RUN docker-php-ext-install pdo pdo_mysql

EXPOSE 80

CMD ["apache2-foreground"]
