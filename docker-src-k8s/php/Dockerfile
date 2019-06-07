FROM php:7.2-fpm-alpine

ENV PHP_TIMEZONE America/New_York
RUN echo "date.timezone = \"$PHP_TIMEZONE\"" > /usr/local/etc/php/conf.d/timezone.ini

RUN cat /dev/urandom | LC_CTYPE=C tr -dc 'a-zA-Z0-9' | fold -w 64 | head -n 1 > /var/www/salt.txt

WORKDIR /var/www

RUN apk add --no-cache mysql-client zlib

RUN set -ex \
  && apk add --no-cache --virtual .build-deps \
             coreutils \
             # GD depends
             freetype-dev \
             libjpeg-turbo-dev \
             libpng-dev \
             # mcrypt depends
             libmcrypt-dev \
             # xmlrpc depends
             libxml2-dev \
             libxslt-dev \
  # Configure and Install PHP extensions
  && docker-php-ext-configure gd  \
       --with-freetype-dir=/usr/include/ \
       --with-jpeg-dir=/usr/include/ \
       --with-png-dir=/usr/include/ \
  && pecl install mcrypt-1.0.1 \
  && docker-php-ext-enable mcrypt \
  && docker-php-ext-install -j "$(nproc)" \
             gd \
             iconv \
             mysqli \
             opcache \
             pdo_mysql \
             xmlrpc \
             xsl \
             zip \
  && runDeps="$( \
    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local \
      | tr ',' '\n' \
      | sort -u \
      | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
  )" \
  && apk add --virtual .drupal-phpexts-rundeps $runDeps \
  # Cleanup
  && rm -rf /tmp/pear ~/.pearrc \
  && chown -R www-data:www-data /usr/local/var/log \
  && docker-php-source delete \
  && apk del .build-deps \
  && rm -rf /tmp/* /var/cache/apk/*

COPY php-conf.d/* /usr/local/etc/php/conf.d/

COPY composer.json /var/www/composer.json

COPY webroot /var/www/webroot

COPY config /var/www/config

COPY config-gm-link-field /var/www/config-gm-link-field

COPY RoboFile.php /var/www/

COPY scripts /var/www/scripts

COPY vendor /var/www/vendor

RUN ln -s /var/www/vendor/bin/* /bin/

COPY entrypoint /usr/local/bin/drupalstand-entrypoint

COPY job-site-install.sh /usr/local/bin/job-site-install.sh

COPY job-site-update.sh /usr/local/bin/job-site-update.sh

RUN chmod +x /usr/local/bin/job-site-install.sh && chmod +x /usr/local/bin/job-site-update.sh

WORKDIR /var/www/webroot

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/drupalstand-entrypoint"]

CMD ["php-fpm"]
