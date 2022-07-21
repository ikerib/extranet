FROM php:8.1-fpm-alpine3.15

ARG FOSJSDUMP=0

ARG DOCKER_TIMEZONE=${DOCKER_TIMEZONE}
RUN echo "${DOCKER_TIMEZONE}" > /etc/timezone

# 2 Set working directory
WORKDIR /usr/src/app

# 3 Install Additional dependencies
RUN apk update && apk add --no-cache \
    build-base shadow vim curl bash \
    zip libzip-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    freetype-dev \
    postgresql-dev \
    aspell-dev \
    autoconf

# 4 Add and Enable PHP-PDO Extenstions
RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-enable pdo_mysql opcache
RUN docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install pgsql
RUN docker-php-ext-install pspell
RUN docker-php-ext-enable pdo_pgsql pgsql pspell
RUN docker-php-ext-install zip
RUN docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype
RUN docker-php-ext-install gd
RUN docker-php-ext-configure intl && docker-php-ext-install intl

# ldap
RUN apk add --update --no-cache \
          libldap && \
      # Build dependancy for ldap \
      apk add --update --no-cache --virtual .docker-php-ldap-dependancies \
          openldap-dev && \
      docker-php-ext-configure ldap && \
      docker-php-ext-install ldap && \
      apk del .docker-php-ldap-dependancies && \
      php -m;

RUN pecl install -o -f redis apcu  \
&&  rm -rf /tmp/pear \
&&  docker-php-ext-enable redis apcu

RUN apk --no-cache add nodejs yarn npm --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community

# 5 Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 6 Remove Cache
RUN rm -rf /var/cache/apk/*

# 7 Add UID '1000' to www-data
#RUN usermod -u ${USER_ID} www-data

# 8 Copy existing application directory permissions
COPY . /usr/src/app

RUN composer install --no-scripts --prefer-dist --no-interaction --optimize-autoloader

RUN php bin/console cache:clear

RUN mkdir -p /usr/src/app/public/uploads
RUN chmod -R 777 /usr/src/app/public/uploads

RUN yarn install

RUN if [[ "$FOSJSDUMP" = "0"]] ; \
    then \
        echo "FOSJSROUTING DISABLED" ; \
    else \
        echo Argument is $FOSJSDUMP \
        RUN php bin/console ckedit:install \
        RUN php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json \
        RUN php bin/console assets:install --symlink public \
    ; fi

RUN yarn encore prod
#RUN chown -R www-data:www-data /usr/src/app

# 9 Change current user
#USER www-data
# 10 Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]

