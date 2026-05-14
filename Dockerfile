FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libpq-dev \
    nodejs \
    npm

# Install PostgreSQL extension
RUN docker-php-ext-install pdo pdo_pgsql pgsql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Install frontend dependencies
RUN npm install
RUN npm run build

# Laravel cache
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Expose port
EXPOSE 10000

# ✅ Create startup script
RUN echo '#!/bin/bash' > /start.sh && \
    echo 'php artisan config:clear' >> /start.sh && \
    echo 'php artisan migrate --force' >> /start.sh && \
    echo 'php artisan storage:link' >> /start.sh && \
    echo 'php artisan serve --host=0.0.0.0 --port=10000' >> /start.sh && \
    chmod +x /start.sh

# Start Laravel
CMD ["/bin/bash", "/start.sh"]