# Utiliser l'image officielle PHP avec Apache
FROM php:8.0-apache

# Activer mod_rewrite d'Apache
RUN a2enmod rewrite

# Installer les extensions nécessaires : gd, mysqli, pdo, etc.
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    git \
    zip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd mysqli pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Exposer le port 80 pour Apache
EXPOSE 80
