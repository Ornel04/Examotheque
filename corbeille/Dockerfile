FROM php:8.2-apache

# Installer les extensions PHP nécessaires pour MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copier les sources PHP dans le dossier web d'Apache
COPY . /var/www/html/

# Donner les bons droits au dossier
RUN chown -R www-data:www-data /var/www/html

# Exposer le port 80 (Apache)
EXPOSE 80
