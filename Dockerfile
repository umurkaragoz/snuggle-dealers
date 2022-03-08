# Generic dockerfile for php laravel development.
# Author umurkaragoz
# Version 1.1.5
# Used php container as base image.
# see: https://hub.docker.com/_/php
FROM php:8.0-apache

ENV ACCEPT_EULA=Y

# The WORKDIR instruction sets the working directory for any RUN, CMD, ENTRYPOINT, COPY and ADD instructions that follow.
WORKDIR /var/www


# ------------------------------------------------------------------------------------------------------------------------ install Common Packages --#
RUN apt-get update && apt-get install -y \
  zip \
  unzip \
  # Required for gd.
  libpng-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  # Required for php-soap.
  libxml2-dev \
  # Required for php-zip.
  libzip-dev \
  # Required for php-sodium
  libsodium-dev


# Install missing php extensions.
RUN  docker-php-ext-install -j$(nproc) iconv \
    && docker-php-ext-install zip soap mysqli pdo pdo_mysql exif \
    # Configure & enable exif extension.
    && docker-php-ext-configure exif --enable-exif \
    # Configure & enable GD extension.
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    # Required for lcobucci/jwt ^4.1.*
    && docker-php-ext-install sodium

# Install composer.
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer


# ---------------------------------------------------------------------------------------------------------------------------------- set Up Apache --#
ENV APACHE_DOCUMENT_ROOT /var/www/public

# Change apache document root to `/var/www/public`.
RUN sed -ri -e 's!/var/www/html!/var/www/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Enable required modules & restart the apache.
RUN a2enmod rewrite \
    && a2enmod headers \
    && a2enmod session \
    && service apache2 restart


# -------------------------------------------------------------------------------------------------------------------------------- install Imagick --#
#RUN apt-get install -y \
#  # see: https://github.com/docker-library/php/issues/105#issuecomment-172652604
#  libmagickwand-dev \
#  && pecl install imagick \
#  && docker-php-ext-enable imagick
