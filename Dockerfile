# Utiliser une image PHP avec Apache
FROM php:8.0-apache

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le code dans le conteneur
COPY . /var/www/html/

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html

# Installer Composer
RUN apt-get update && apt-get install -y unzip curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer les dépendances PHP si tu en as
WORKDIR /var/www/html
RUN composer install

EXPOSE 8000
