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

RUN mkdir -p /etc/nginx/ssl \
    && openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/nginx/ssl/selfsigned.key \
    -out /etc/nginx/ssl/selfsigned.crt \
    -subj "/C=US/ST=Denial/L=Springfield/O=Dis/CN=localhost"

RUN apk add --no-cache openssl

RUN apk add --update supervisor

RUN apk add go

RUN apk add fish
RUN curl -L --create-dirs -o ~/.config/fish/completions/artisan.fish https://github.com/adriaanzon/fish-artisan-completion/raw/master/completions/artisan.fish
RUN curl -L --create-dirs -o ~/.config/fish/completions/php.fish https://github.com/adriaanzon/fish-artisan-completion/raw/master/completions/php.fish
RUN curl -L --create-dirs -o ~/.config/fish/functions/artisan.fish https://github.com/adriaanzon/fish-artisan-completion/raw/master/functions/artisan.fish

RUN rm  -rf /tmp/* /var/cache/apk/*

ADD ./.docker/supervisord.conf /etc/
COPY ./.docker/local/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/local/app.ini /etc/php81/conf.d/custom.ini
COPY ./.docker/local/fpm.conf /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /app
RUN chown nginx:nginx -R /app
# RUN usermod –a –G ngin www-data

RUN git config --global --add safe.directory /app

USER nginx

WORKDIR /app

USER root

ENTRYPOINT ["supervisord", "--nodaemon", "--configuration", "/etc/supervisord.conf"]