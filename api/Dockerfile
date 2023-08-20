ARG PHP_VERSION=8.2.8
FROM php:${PHP_VERSION}-apache

MAINTAINER Mark Anthony Naluz "anthony.naluz15@gmail.com"

# Set up the Docker Working Directory
ENV HOME=/var/www/html/api
WORKDIR $HOME

# Update Server Date time
ENV TZ=Asia/Manila
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    cron \
    libpq-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    git \
    zip \
    unzip \
    curl \
    libsodium-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    zip \
    sodium \
    && pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

COPY composer.json composer.lock $HOME/

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable Apache modules
RUN a2enmod \
    rewrite \
    headers \
    expires \
    deflate

# Change default docker container ownership to www-data
RUN usermod -u 1000 www-data
RUN usermod -G staff www-data

# Stdout logs
RUN ln -sf /dev/stdout /var/log/apache2/api_error.log
RUN chown -R $USER:www-data /var/log/apache2/api_error.log

# Setup Apache VirtualHost and PHP configuration
COPY ./api.conf /etc/apache2/sites-available/
COPY ./php.ini /usr/local/etc/php/

RUN cd /etc/apache2/sites-available/ \
    && a2ensite api.conf \
    && a2dissite 000-default.conf \
    && service apache2 restart

# Copy the entrypoint script
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Move all files to $HOME
COPY . $HOME

CMD ["entrypoint.sh"]
