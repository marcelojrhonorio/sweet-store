# Set the base image
FROM php:7.1
# Dockerfile author / maintainer
MAINTAINER Smith Junior <smith.junior@icloud.com>
# Update application repository list and install

ARG SSH_KEY
ENV SSH_KEY $SSH_KEY

ARG SSH_KEY_PUBLIC
ENV SSH_KEY_PUBLIC $SSH_KEY

RUN apt-get update -yqq

RUN apt install -yqq git sqlite3 libcurl4-gnutls-dev libicu-dev libmcrypt-dev libvpx-dev libjpeg-dev libpng-dev libxpm-dev zlib1g-dev libfreetype6-dev libxml2-dev libexpat1-dev libbz2-dev libgmp3-dev libldap2-dev unixodbc-dev libpq-dev libsqlite3-dev libaspell-dev libsnmp-dev libpcre3-dev libtidy-dev

RUN yes | pecl install xdebug \
  && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_port=9003" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_host = 172.17.0.1" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN docker-php-ext-configure mcrypt

RUN docker-php-ext-install mbstring mcrypt pdo_mysql curl json intl gd xml zip bz2 opcache

RUN apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -LO https://deployer.org/deployer.phar && mv deployer.phar /usr/local/bin/dep && chmod +x /usr/local/bin/dep

RUN mkdir -p /root/.ssh && touch /root/.ssh/known_hosts && chmod 0700 /root/.ssh
RUN ssh-keyscan gitlab.com >> /root/.ssh/known_hosts && ssh-keyscan store.uat-sweetbonus.com.br >> /root/.ssh/known_hosts && ssh-keyscan api.uat-sweetmedia.com.br >> /root/.ssh/known_hosts && ssh-keyscan uat-sweetmedia.com.br >> /root/.ssh/known_hosts && ssh-keyscan uat-sweetbonus.com.br >> /root/.ssh/known_hosts
RUN ssh-keyscan store.sweetbonus.com.br >> /root/.ssh/known_hosts && ssh-keyscan api.sweetmedia.com.br >> /root/.ssh/known_hosts && ssh-keyscan sweetmedia.com.br >> /root/.ssh/known_hosts && ssh-keyscan sweetbonus.com.br >> /root/.ssh/known_hosts
RUN echo "$SSH_KEY" > /root/.ssh/id_rsa && chmod 600 /root/.ssh/id_rsa && echo "$SSH_KEY_PUBLIC" > /root/.ssh/id_rsa.pub && chmod 600 /root/.ssh/id_rsa.pub

WORKDIR /var/www/html
ADD . /var/www/html

CMD php artisan serve --port=80 --host=0.0.0.0

EXPOSE 80
HEALTHCHECK --interval=1m CMD curl -f http://localhost/ || exit 1
