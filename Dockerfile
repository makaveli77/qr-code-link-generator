# --- Base Stage ---
FROM php:8.4-apache AS base
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip && \
    pecl install redis && docker-php-ext-enable redis

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache Mod Rewrite
RUN a2enmod rewrite

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Add non-root user (only after build steps)
RUN useradd -m appuser && chown -R appuser:appuser /var/www/html

# --- Development Stage ---
FROM base AS development
COPY . .
RUN composer install
RUN npm install && npm run build
RUN chown -R appuser:appuser /var/www/html
USER appuser
CMD ["apache2-foreground"]

# --- Build Stage ---
FROM base AS builder
COPY . .
RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build
RUN chown -R appuser:appuser /var/www/html
USER appuser

# --- Production Stage ---
FROM base AS production
ENV APP_ENV=production
COPY --from=builder /var/www/html /var/www/html
RUN chown -R appuser:appuser /var/www/html
USER appuser
CMD ["apache2-foreground"]
