FROM php:8.0-apache

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    curl git unzip zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier uniquement les fichiers nécessaires
COPY composer.json composer.lock /var/www/html/
COPY src/ /var/www/html/src/
COPY public/ /var/www/html/public/
COPY config/ /var/www/html/config/

# Définir le répertoire de travail et installer les dépendances PHP
WORKDIR /var/www/html
RUN composer install

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html

# Exposer le port pour Apache
EXPOSE 80
