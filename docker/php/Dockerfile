FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \  
    zip \
    unzip \
    npm \
    libicu-dev \
    libzip-dev \
    zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (grouped for better caching)
RUN docker-php-ext-install \
    pdo \         
    pdo_mysql \    
    pdo_pgsql \    
    pgsql \        
    mbstring \
    exif \
    pcntl \
    bcmath \
    intl \
    zip \
    gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY src /var/www

# Install dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader
RUN npm install && npm run build

# Permissions
RUN chown -R www-data:www-data /var/www

USER www-data 
EXPOSE 9000
CMD ["php-fpm"]



# FROM php:8.2-fpm

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     git \
#     curl \
#     libpng-dev \
#     libonig-dev \
#     libxml2-dev \
#     zip \
#     unzip \
#     npm

# # Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions
# RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# RUN docker-php-ext-install pdo_pgsql pgsql

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www

# # Copy application files
# COPY src /var/www

# # Install dependencies

# # [Installs only production dependencies and optimizes autoloading Production deployment]
# ## RUN composer install --optimize-autoloader --no-dev 

# #[Installs only production dependencies and optimizes autoloading Production deployment]
# RUN composer install --no-interaction --prefer-dist --optimize-autoloader 

# RUN npm install && npm run build

# # Change ownership
# RUN chown -R www-data:www-data /var/www

# USER www-data

# EXPOSE 9000
# CMD ["php-fpm"]