#!/bin/bash

exec docker run --rm --name php_8_0 \
  -e RIOT_PROVIDERS=${RIOT_PROVIDERS} \
  -e INFLUXDB_URL=${INFLUXDB_URL} \
  --network host \
  -v $(pwd):/$(pwd) --workdir=/$(pwd) php:8.0-rc-alpine "$@"
