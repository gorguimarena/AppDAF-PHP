FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && \
    apt-get install -y \
        nginx \
        supervisor \
        git \
        unzip \
        curl \
        libpq-dev \
        libyaml-dev \
        && docker-php-ext-install pdo pdo_pgsql pgsql \
        && pecl install yaml \
        && docker-php-ext-enable yaml \
        && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Dossier de travail
WORKDIR /var/www/html

# Copier les fichiers
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader || true

# Supprimer le fichier default nginx conf
RUN rm /etc/nginx/sites-enabled/default

# Copier ta configuration nginx et supervisord
COPY default.conf /etc/nginx/conf.d/default.conf
COPY supervisord.conf /etc/supervisord.conf

# Exposer le port
EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
