FROM php:8.0-apache as php-apache

#RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
RUN apt-get update -y
#RUN apt-get install libyaml-dev -y
RUN apt-get install gettext -y
RUN apt-get install -y locales locales-all
# ENV LC_ALL en_US.UTF-8
# ENV LANG en_US.UTF-8
# ENV LANGUAGE en_US.UTF-8
# RUN dpkg-reconfigure locales
#RUN pecl install yaml && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini && docker-php-ext-enable yaml

COPY src/ /var/www/html/
COPY src/images/favicon /var/www/html/
COPY apache2.conf /etc/apache2/apache2.conf

RUN a2enmod rewrite