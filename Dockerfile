FROM php:8.2

RUN apt-get update && apt-get install -y unzip libzip-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql zip gd
RUN apt-get -y install openssh-client
RUN COMPOSER_ALLOW_SUPERUSER=1 curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /var/www/html
COPY . .

CMD ["php", "artisan", "serve", "--host=0.0.0.0"]
