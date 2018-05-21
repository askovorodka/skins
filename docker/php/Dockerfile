FROM alpine:edge
MAINTAINER Alan Bondarchuk <imacoda@gmail.com>

ARG NEWRELIC_LICENSE
ENV NEWRELIC_VERSION 7.2.0.191
ENV PHP_INI_DIR /etc/php7

# Install packages
RUN echo 'http://alpine.gliderlabs.com/alpine/edge/community' >> /etc/apk/repositories && \

    apk add --update \
        git \
        curl \
        pcre \
        tzdata \
        gd \
        mysql-client \
        # PHP packages
        php7 \
        php7-fpm \
        php7-cgi \
        php7-pdo \
        php7-pdo_mysql \
        php7-mysqli \
        php7-mbstring \
        php7-bcmath \
        php7-opcache \
	    php7-pcntl \
        php7-session \
	    php7-pear \
        php7-ctype \
        php7-json \
        php7-posix \
        php7-curl \
        php7-dom \
        php7-sockets \
        php7-zlib \
        php7-bz2 \
        php7-phar \
        php7-openssl \
        php7-posix \
        php7-zip \
        php7-iconv \
        php7-imap \
	    php7-xml \
	    php7-xmlwriter \
	    php7-tokenizer \
	    php7-simplexml \
        && \

    # Set Europe/Moscow as local timezone
    cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime && \
    echo "Europe/Moscow" > /etc/timezone && \

    # Create symlinks for backward compatibility
    ln -sf /usr/bin/php7 /usr/bin/php && \
    ln -sf /usr/sbin/php-fpm7 /usr/bin/php-fpm && \

    # Install composer
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \

    # Install parallel downloads plugin
    composer global require hirak/prestissimo:^0.3 --optimize-autoloader && \

    # Install newrelic-agent
    curl "https://download.newrelic.com/php_agent/archive/$NEWRELIC_VERSION/newrelic-php5-$NEWRELIC_VERSION-linux-musl.tar.gz" | tar -zx && \
    cd "newrelic-php5-$NEWRELIC_VERSION-linux-musl" && \
    NR_INSTALL_SILENT=1 NR_INSTALL_KEY="$NEWRELIC_LICENSE" ./newrelic-install install && \

    # Cleanup
    apk del --purge \
        *-dev \
        build-base \
        autoconf \
        libtool \
        tzdata \
        && \
    rm -rf \
        /usr/include/php \
        /usr/lib/php/build \
        /var/cache/apk/* \
        /tmp/* \
        /root/.composer

# Enable newrelic
RUN sed -i \
        -e "s/^newrelic.logfile.*/newrelic.logfile = \"\/proc\/self\/fd\/2\"/" \
        -e "s/^newrelic.appname.*/newrelic.appname = \"Skins4REAL\"/" \
        -e "s/^newrelic.daemon.logfile.*/newrelic.daemon.logfile = \"\/proc\/self\/fd\/2\"/" \
        -e "s/^;newrelic.daemon.loglevel.*/newrelic.daemon.loglevel = \"warning\"/" \
        -e "s/^;newrelic.loglevel.*/newrelic.loglevel = \"warning\"/" \
        -e "s/^;newrelic.enabled.*/newrelic.enabled = true/" \
        -e "s/^;newrelic.framework.*/newrelic.framework = \"symfony2\"/" \
        $PHP_INI_DIR/conf.d/newrelic.ini

# Copy PHP configs
COPY 00_opcache.ini $PHP_INI_DIR/conf.d/
COPY php-fpm.conf $PHP_INI_DIR/

#phpunit
RUN apk update \
    &&  apk add ca-certificates wget \
    &&  update-ca-certificates  \
    &&  wget https://phar.phpunit.de/phpunit.phar \
    &&  chmod +x phpunit.phar \
    &&  mv phpunit.phar /usr/local/bin/phpunit \
    &&  phpunit --version

# Create user www-data
RUN addgroup -g 82 -S www-data && \
    adduser -u 82 -D -S -G www-data www-data

# Create work dir
RUN mkdir -p /var/www/html && \
    chown -R www-data:www-data /var/www

WORKDIR /var/www/html
VOLUME /var/www/html
EXPOSE 9000 7000

USER root
CMD ["php-fpm", "-R"]