FROM php:7.3.3-apache

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    zlib1g-dev \
    zip - libzip-dev\
    unzip \
    && rm -r /var/lib/apt/lists/*

RUN docker-php-ext-configure zip --with-libzip \ 
    && docker-php-ext-install \
        intl \
        mbstring \
        pcntl \
        pdo_mysql \
        zip \
        opcache


#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

#set our application folder as an environment variable
ENV APP_HOME /var/www/html

#change uid and gid of apache to docker user uid/gid
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

#RUN sed -i -e "s/html/html\/webroot/g" /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

#copy source files and run composer
COPY . $APP_HOME
RUN composer install --no-interaction

RUN chown -R www-data:www-data $APP_HOME