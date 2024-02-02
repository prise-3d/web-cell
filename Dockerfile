# Utilisez une image de base Ubuntu
FROM php:8.0-apache

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Mise à jour des paquets et installation des dépendances
RUN apt-get update && apt-get install -y \
    apache2 \
    g++ \
    git \
    cmake

# Configuration d'Apache pour exposer le répertoire /var/www/html
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Clonage du référentiel Git contenant le code C++
RUN git clone https://gitlab.com/crrenaud/cellv1.5 /app

# Compilation du code C++

WORKDIR /app/bin
RUN cmake ..
RUN make

COPY html /var/www/html
RUN mkdir /var/www/html/uploads
RUN chmod 777 /var/www/html/uploads
RUN mv /app/Doc/celldoc1.5.1GB.pdf /var/www/html

# Exposition du port 80 pour Apache
EXPOSE 80

# Commande de démarrage d'Apache en mode foreground
CMD ["apachectl", "-D", "FOREGROUND"]