FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql
RUN echo "display_errors=On" > /usr/local/etc/php/conf.d/dev.iniFROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql
RUN echo "display_errors=On" > /usr/local/etc/php/conf.d/dev.ini