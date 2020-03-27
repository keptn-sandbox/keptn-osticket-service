FROM php:apache

COPY files /var/www/html
RUN chown -R www-data:www-data /var/www

CMD ["apache2-foreground"]
