
# Toy Library - Laravel API
# PHP 8.3 with PostgreSQL support

FROM php:8.3-cli-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    ca-certificates \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        mbstring \
        bcmath \
        zip \
        xml \
        dom \
        curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer from official image
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/html

# Configure PHP
RUN { \
    echo 'memory_limit=512M'; \
    echo 'upload_max_filesize=10M'; \
    echo 'post_max_size=12M'; \
    echo 'max_execution_time=120'; \
    echo 'date.timezone=UTC'; \
    } > /usr/local/etc/php/conf.d/toy-library.ini

# Expose Laravel development server port
EXPOSE 8000

# Default startup command
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
