RiotKit HealthFlux
==================

Pushes information about healthchecks status into InfluxDB basing on data from UptimeRobot service and Infracheck endpoints.

Usage with docker
-----------------

...

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
