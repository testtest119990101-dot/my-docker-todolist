FROM php:8.1-apache

RUN docker-php-ext-install mysqli

WORKDIR /var/www/html

COPY . .

RUN echo '<?php phpinfo(); ?>' > index.php

EXPOSE 80

CMD ["apache2-foreground"]
