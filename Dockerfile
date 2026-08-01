# Usa una imagen oficial de PHP 8.2 con servidor embebido
FROM php:8.2-cli

# Instala extensiones que puedas necesitar
RUN apt-get update && apt-get install -y unzip zip

# Copia el contenido de la carpeta al contenedor
COPY . /var/www/html

# Expone el puerto requerido por Render
EXPOSE 10000

# Inicia el servidor web embebido de PHP
CMD ["php", "-S", "0.0.0.0:10000", "-t", "/var/www/html"]
