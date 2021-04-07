FROM php:7.4-fpm
RUN apt-get update -y && apt-get install -y openssl zip unzip git nano
#RUN apt-get update -y && apt-get install -y openssl zip unzip git libxml2-dev curl nano

WORKDIR /var/www/modullo-core

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo pdo_mysql
#RUN docker-php-ext-install pdo pdo_mysql mbstring bcmath xml ctype fileinfo json tokenizer curl


RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini && \
    sed -i -e "s/^ memory_limit./memory_limit = 4G/g" -e "s/^ max_execution_time./max_execution_time = 0/g" /usr/local/etc/php/php.ini


# Install dependencies
COPY composer.json /var/www/modullo-core/composer.json
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

# Copy codebase
COPY . /var/www/modullo-core

# Finish composer
RUN composer dump-autoload --no-scripts --no-dev --optimize


RUN chown -R www-data:www-data /var/www/modullo-core/storage

RUN chmod -R u=rwx,g=rwx,o=rwx /var/www/modullo-core/storage
RUN chmod -R u=rwx,g=rwx,o=rw /var/www/modullo-core/storage/logs
RUN touch /var/www/modullo-core/storage/logs/lumen.log && > /var/www/modullo-core/storage/logs/lumen.log
RUN chown www-data:www-data /var/www/modullo-core/storage/logs/lumen.log
RUN chmod u=rwx,g=rw,o=rw /var/www/modullo-core/storage/logs/lumen.log
RUN chmod u=rwx,g=rx,o=x /var/www/modullo-core/artisan

RUN chmod 660 /var/www/modullo-core/storage/oauth-public.key

# RUN php artisan passport:install

RUN mkdir -p /var/log/php/ && touch /var/log/php/modullo.log

RUN composer dump-autoload

#EXPOSE 18001

EXPOSE 9000
CMD ["php-fpm"]