FROM php:8.2-apache

# Copie les sources PHP dans le dossier web d'Apache
COPY . /var/www/html/

# Installer l'extension PDO PostgreSQL pour PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Exposer le port 80 (Apache)
EXPOSE 80
