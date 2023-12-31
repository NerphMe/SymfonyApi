FROM php:8.1.0-fpm-alpine as php-fpm-alpine

WORKDIR /var/www/html

RUN apk update && apk upgrade && \
    apk add --no-cache bash git openssh build-base shadow vim curl php8-pear php8-dev \
    php8-pdo_mysql php8-mysqli php8-mbstring php8-xml php8-openssl php8-json \
    php8-phar php8-zip php8-gd php8-dom php8-session php8-redis php8-bcmath \
    php8-pcntl php8-zlib && \
    docker-php-ext-install pdo_mysql bcmath pcntl && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    rm -rf /var/cache/apk/* && \
    usermod -u 1000 www-data

USER root

FROM php-fpm-alpine as php-dev

RUN cd /tmp && \
    git clone https://github.com/xdebug/xdebug.git && \
    cd xdebug && \
    git checkout xdebug_3_1 && \
    phpize && \
    ./configure --enable-xdebug && \
    make && \
    make install && \
    rm -rf /tmp/xdebug && \
    docker-php-ext-enable xdebug

RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9009" >> /usr/local/etc/php/conf.d/xdebug.ini


COPY --chown=www-data:www-data . /var/www/html
RUN chmod -R 777 /var/www/html

RUN chmod +x ./entrypoint.dev.sh

CMD ["su", "-", "www-data", "-s", "/bin/sh", "-c", "php-fpm"]