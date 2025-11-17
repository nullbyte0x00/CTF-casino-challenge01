# Dockerfile

FROM php:8.2-apache

# Install PDO and PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

# (Optional) Enable Apache rewrite module
RUN a2enmod rewrite
