RiotKit HealthFlux
==================

Pushes information about healthchecks status into InfluxDB basing on data from UptimeRobot service and Infracheck endpoints.

Usage with docker
-----------------

```yaml
    version: "2.4"
    services:
        healthflux:
            image: quay.io/riotkit/riothealthflux:v3.0.0
            environment:
                INFLUXDB_URL: "http+influxdb://${INFLUXDB_USER}:${INFLUXDB_USER_PASSWORD}@influxdb:8086/${STATS_DB_NAME}"
                RIOT_PROVIDERS: "UptimeRobot://aaaaa-bbbbbb;UptimeRobot://xxxx-yyyyyy;Infracheck://cccccc-ddddddd"
```

Usage without docker
--------------------

**Requirements:**
- PHP 8.0+
- Composer
- php-curl (PHP extension)
- php-json (PHP extension)

```bash
composer install

export RIOT_PROVIDERS="Infracheck://http://127.0.0.1:8000;UptimeRobot://..." 
export INFLUXDB_URL="http+influxdb://bakunin:bakunin@localhost:8086/hulajpole"

./bin/console background-process
```

Keywords
--------

UptimeRobot to InfluxDB. Healthchecks in Grafana.
