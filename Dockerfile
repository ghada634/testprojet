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
    && docker-php-ext-install gd mysqli pdo pdo_mysql

# Copier les fichiers du projet dans le conteneur
COPY . /var/www/html/

# Changer les permissions pour Apache
RUN chown -R www-data:www-data /var/www/html

# Installer Composer (gestionnaire de dépendances PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Exposer le port 80 (par défaut pour Apache)
EXPOSE 80
