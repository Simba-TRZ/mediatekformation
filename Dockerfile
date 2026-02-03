FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && apt-get clean

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Copier la configuration Apache personnalisée
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier tous les fichiers du projet
COPY . .

# Installer les dépendances sans scripts automatiques
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Vider le cache Symfony
RUN php bin/console cache:clear --env=prod || true

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/public

# Apache écoute sur le port 80
EXPOSE 80

# Démarrer Apache avec le port dynamique de Render
CMD sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf && \
    sed -i "s/:80/:${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf && \
    apache2-foreground