FROM php:8.2-apache

# 1. First fix the GPG key issues
RUN apt-get update --allow-releaseinfo-change && \
    apt-get install -y --no-install-recommends ca-certificates && \
    update-ca-certificates && \
    rm -rf /var/lib/apt/lists/*

# 2. Install dependencies with forced install (ignore signature errors)
RUN apt-get update -o Acquire::AllowInsecureRepositories=true && \
    apt-get install -y --allow-unauthenticated \
    libzip-dev \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libgmp-dev

# 3. Configure GD with explicit paths (bulletproof method)
RUN docker-php-ext-configure gd \
    --with-jpeg=/usr/include/ \
    --with-freetype=/usr/include/

# 4. Install extensions separately to avoid conflicts
RUN docker-php-ext-install gd && \
    docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    bcmath \
    gmp \
    xml

# 5. Clean up to reduce image size
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# 6. Apache setup
RUN a2enmod rewrite

# 7. Composer installation
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# 8. Application setup
WORKDIR /var/www/html
COPY . .
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# 9. Fix Apache document root
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf
