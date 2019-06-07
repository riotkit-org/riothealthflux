FROM node:11.15.0-stretch-slim

ADD dev/entrypoint.sh /entrypoint.sh
RUN apt-get update \
    && apt-get install -y yarn make gcc g++ bash \
    && mkdir /app \
    && cd /app \
    && chown node:node /app \
    && chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh

EXPOSE 8080
