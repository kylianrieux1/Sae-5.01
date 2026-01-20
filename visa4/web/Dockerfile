FROM php:8.2-apache

# 1. Installation des dépendances système (on ajoute les libs pour les images)
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Configuration et installation des extensions PHP
# On installe pdo_mysql pour la base et gd pour le captcha
RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# 3. Configuration Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite

WORKDIR /var/www/html

# Copier le code
COPY ./projet /var/www/html

# Droits d’accès
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

EXPOSE 8080

CMD ["apache2-foreground"]
