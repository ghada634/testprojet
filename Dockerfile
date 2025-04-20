FROM php:8.0-apache

# Activer mod_rewrite et installer les dépendances nécessaires
RUN apt-get update \
    && apt-get install -y \
        curl \
        git \
        gnupg \
        libcurl4-openssl-dev \
        libfreetype6-dev \
        libicu-dev \
        libjpeg-dev \
        libonig-dev \
        libpng-dev \
        libpq-dev \
        libssl-dev \
        libxml2-dev \
        libxslt1-dev \
        libzip-dev \
        unzip \
        zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite

# Copier le code dans le conteneur
COPY . /var/www/html/

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer les dépendances PHP via Composer
WORKDIR /var/www/html
RUN composer install

# Exposer le port pour Apache
EXPOSE 80
