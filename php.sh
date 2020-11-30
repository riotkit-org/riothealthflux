#!/bin/bash

exec docker run --rm --name php_8_0 \
  -e RIOT_PROVIDERS=${RIOT_PROVIDERS} \
  -e INFLUXDB_ORG=${INFLUXDB_ORG} \
  -e INFLUXDB_BUCKET=${INFLUXDB_BUCKET} \
  -e INFLUXDB_TOKEN=${INFLUXDB_TOKEN} \
  --network host \
  -v $(pwd):/$(pwd) --workdir=/$(pwd) php:8.0-rc-alpine "$@"
