FROM php:7.4-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer clear-cache \
    && rm -rf ~/.composer

COPY . /usr/src/charlie_wfh
WORKDIR /usr/src/charlie_wfh
