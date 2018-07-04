FROM wolnosciowiec/docker-php-app

ENV UAB_TITLE="System monitoring"\
    UAB_PROVIDERS="" \
    UAB_CSS="./assets/css/zapatista.css" \
    UAB_CACHE_ID="uab_" \
    UAB_CACHE_TTL=60 \
    UAB_CACHE="file" \
    UAB_REDIS_HOST="localhost" \
    UAB_REDIS_PORT=6379 \
    UAB_TOR_MANAGEMENT_PORT=9052 \
    UAB_TOR_PASSWORD="" \
    UAB_PROXY="" \
    UAB_EXPOSE_URLS=1

ADD . /var/www/html

RUN cd /var/www/html \
    && chown www-data:www-data /var/www/html -R \
    && su www-data -s /bin/bash -c "make deploy"
