FROM php:7.4.0-cli-alpine

WORKDIR /app
RUN docker-php-ext-install mysqli
RUN echo 'memory_limit = 4G' >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini;

# Add docker-compose-wait tool -------------------
ENV WAIT_VERSION 2.7.2
ADD https://github.com/ufoscout/docker-compose-wait/releases/download/$WAIT_VERSION/wait /wait
RUN chmod +x /wait


CMD ["sh","-c","/wait && php /app/jobs/optimizationjob.php"]
