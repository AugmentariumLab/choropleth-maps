# Dockerfile
FROM php:7.4-apache
RUN apt-get update && \
    apt-get install -y nodejs npm zip libpq-dev && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pdo pdo_pgsql pgsql && \
    a2enmod rewrite
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's#AllowOverride [Nn]one#AllowOverride All#' /etc/apache2/apache2.conf
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
RUN sed -i 's#upload_max_filesize = 2M#upload_max_filesize = 200M#' "$PHP_INI_DIR/php.ini"
RUN sed -i 's#post_max_size = 8M#post_max_size = 200M#' "$PHP_INI_DIR/php.ini"
ADD package.json package-lock.json /var/www/html/
RUN npm ci
ADD . /var/www/html/
RUN cp .env.example .env && composer install
RUN chmod 775 /root && \
    chown www-data:www-data -R /var/www/html && \
    npm run prod

CMD ./scripts/login_db/init.sh && apache2-foreground