FROM php:7.4.10-fpm-buster

# Install dependencies
RUN apt-get update -y && \
    apt-get install --no-install-recommends -y libicu-dev libxslt1-dev unzip git nginx-light supervisor procps && \
    mkdir -p /var/log/supervisor && \
    docker-php-ext-install intl bcmath pdo pdo_mysql mysqli xsl && \
    echo 'memory_limit = 2G' > /usr/local/etc/php/conf.d/memory-limit-php.ini && \
    echo 'post_max_size = 50M' > /usr/local/etc/php/conf.d/upload-limit-php.ini && \
    echo 'upload_max_filesize = 50M' >> /usr/local/etc/php/conf.d/upload-limit-php.ini && \
        pecl install redis && docker-php-ext-enable redis && \
    rm -rf /var/lib/apt/lists/* && \
    rm -Rf /usr/local/etc/php-fpm.*


# install composer
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer
RUN composer config --global cache-dir /composer_cache


# application entrypoint
ADD deployment/entrypoint-local-dev.sh /entrypoint-local-dev.sh

ADD deployment/config-files/php-fpm.conf /usr/local/etc/php-fpm.conf

ADD /deployment/config-files/nginx.conf /etc/nginx/nginx.conf
RUN mkdir -p /var/lib/nginx /usr/local/var/log/ & \
    chown -R www-data /var/lib/nginx /usr/local/var/log/ /etc/nginx/

# cleanup & chown
RUN mkdir -p /app/Data/Persistent /app/Configuration/Development/Docker /composer_cache && \
    chown -R www-data /app /composer_cache && \
    apt-get clean

WORKDIR /app

USER www-data
ENTRYPOINT ["/entrypoint-local-dev.sh"]
