FROM php:8.0-apache

# Activer mod_rewrite et installer les dépendances nécessaires
RUN apt-get update \
    && apt-get install -y \
        curl \
        libfreetype6-dev \
        libjpeg-dev \
        libpng-dev \
        unzip \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        zlib1g-dev \
        libpq-dev \
        libicu-dev \
        libxslt1-dev \
        libssl-dev \
        libcurl4-openssl-dev \
        git \
        zip \
        gnupg \
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
