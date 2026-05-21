FROM php:7.4-fpm

# Instala dependências do sistema e extensões do PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Limpa o cache do gerenciador de pacotes
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensões do PHP necessárias para o Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Baixa o Composer mais estável para a versão 2.2 (ideal para PHP antigo)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
