FROM library/php:8.1-cli
WORKDIR /usr/local/share/metrics

RUN apt update -yyq && apt install -yyq zip libzip-dev
RUN docker-php-ext-install zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/usr/local/bin/ --filename composer; rm composer-setup.php

COPY app .
RUN composer install
CMD php ./index.php
