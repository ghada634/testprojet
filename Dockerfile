# Utiliser une image PHP avec Apache
FROM php:8.0-apache

# Activer mod_rewrite
RUN a2enmod rewrite

# Installer les extensions PHP nécessaires, y compris mysqli
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Copier le code dans le conteneur
COPY . /var/www/html/

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html

# Installer Composer
RUN apt-get update && apt-get install -y unzip curl \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer les dépendances PHP via Composer
WORKDIR /var/www/html
RUN composer install

# Exposer le port pour Apache
EXPOSE 80
