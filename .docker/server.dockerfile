FROM php:8.1-fpm-alpine3.20

# Install system dependencies
RUN apk add \
    libpng-dev \
    libjpeg-turbo-dev \
    gmp-dev \
    freetype-dev \
    libzip-dev \
    autoconf \
    g++ \
    make \
    openssl-dev \
    git

# Install PHP extensions needed by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    gd \
    zip \
    bcmath \
    opcache \
    gmp

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN apk add nginx

RUN apk add --update supervisor

RUN apk add go

RUN rm  -rf /tmp/* /var/cache/apk/*

RUN composer self-update

ADD ./.docker/supervisord.conf /etc/
COPY ./.docker/server/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/server/app.ini /etc/php81/conf.d/custom.ini
COPY ./.docker/server/fpm.conf /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /app
RUN chown nginx:nginx -R /app
# RUN usermod –a –G ngin www-data

USER nginx

WORKDIR /app
COPY ./ /app/
COPY ./.docker/server/composer-auth.json /app/auth.json

RUN git config --global --add safe.directory /app
RUN git config --global --add safe.directory /app/vendor/vi/vbalance-features

RUN composer install --ignore-platform-reqs

USER root

ENTRYPOINT ["supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]