FROM php:8.2-fpm

ARG user=usuario
ARG uid=1001

# Criar usuário e grupo
RUN groupadd -g $uid $user && \
    useradd -m -u $uid -g $uid -s /bin/bash $user

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    gcc \
    zip \
    unzip \
    wget \
    libaio1 \
    libaio-dev \
    libssl-dev \
    make \
    libsqlite3-dev \  
    libfreetype6-dev \ 
    libjpeg62-turbo-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_sqlite

RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install gd
RUN docker-php-ext-install sockets

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN pecl install -o -f xdebug && \
    docker-php-ext-enable xdebug

COPY nginx/default.conf /etc/nginx/sites-available/default

WORKDIR /var/www
COPY . .

RUN chown -R www-data:www-data /var/www && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80

CMD service php8.2-fpm start && nginx -g 'daemon off;'
