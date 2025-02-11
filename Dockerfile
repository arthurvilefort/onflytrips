# Usa a imagem do PHP com FPM (FastCGI Process Manager)
FROM php:8.2-fpm

# Instala extensões necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo pdo_mysql

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www

# Copia os arquivos do projeto para o container
COPY . .

# Define permissões para o diretório de cache e logs do Laravel
RUN chmod -R 777 storage bootstrap/cache

# Instala dependências do Laravel
RUN composer install

# Expõe a porta 8000
EXPOSE 8000

# Comando de inicialização do container
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
