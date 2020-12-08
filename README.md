RiotKit HealthFlux
==================

Pushes information about healthchecks status into InfluxDB 1.8+/2.0+ basing on data from UptimeRobot service and Infracheck endpoints.

![.github/img.png](.github/img.png)

Hardware requirements
---------------------

- CPU: 1vCPU (almost does not use CPU, any will be ok)
- Memory: 64MB RAM
- Disk space: 60MB (in docker 100 MB)

InfluxDB 1.8+ query
-------------------

```sql
SELECT last("up_int") FROM "riothealthflux" WHERE $timeFilter GROUP BY "name" 
```

Metrics + example values
------------------------

- checked_by: "Infracheck" / "UptimeRobot"
- name: "SSL Certificate: iwa-ait.org"
- url: "https://infracheck.my-network.local/my-strong-secret-code/"
- ident: "SSL Certificate: iwa-ait.org_https://infracheck.my-network.local/my-strong-secret-code/"
- up: "true" / "false"

- tag(up): "true" / "false"
- tag(up_int): "1" / "0"
- tag(id): "md5-hash-there"
- tag(url): "https://infracheck.my-network.local/my-strong-secret-code/"
- tag(name): "SSL Certificate: iwa-ait.org"

Usage with docker
-----------------

```yaml
    version: "2.4"
    services:
        healthflux:
            image: quay.io/riotkit/riothealthflux:3.0.0
            environment:
                INFLUXDB_URL: "http+influxdb://${INFLUXDB_USER}:${INFLUXDB_USER_PASSWORD}@influxdb:8086/${STATS_DB_NAME}"
                
                # list of providers, separated by ";"
                # Supported providers: Infracheck, UptimeRobot
                # 
                # Infracheck with a secret code:
                #     Infracheck://http://health.my-network.somewhere/infracheck-secret-code-there
                # 
                # Infracheck without secret code:
                #     Infracheck://http://health.my-network.somewhere/
                #
                # UptimeRobot (token you can generate in UptimeRobot web user interface in dashboard settings):
                #     UptimeRobot://your-token-there
                #
                RIOT_PROVIDERS: "UptimeRobot://aaaaa-bbbbbb;UptimeRobot://xxxx-yyyyyy;Infracheck://cccccc-ddddddd"
                
                # time interval in seconds how often to write data to InfluxDB
                # (consider fact that eg. UptimeRobot in free plan is running checks every 5 minutes. 
                #  Infracheck is open and free, and can run checks more often as you wish)
                SLEEP_TIME: "60"

                # allows to customize the measurement name that is pushed to the InfluxDB
                INFLUXDB_MEASUREMENT_NAME: "riothealthflux"
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

# optional: Configure measurement name
export INFLUXDB_MEASUREMENT_NAME="riothealthflux"

# run each time you want to push the data to InfluxDB
# put it in a loop with a sleep, or schedule with a crontab
./bin/console background-process
```

Releasing
---------

1. Create a release in Github releases
2. Wait for pipeline to build and publish to docker registry

Keywords
--------

UptimeRobot to InfluxDB. Healthchecks in Grafana.

From authors
------------

We are grassroot activists for social change, so we created RKD especially in mind for those fantastic initiatives:

- RiotKit (https://riotkit.org)
- International Workers Association (https://iwa-ait.org)
- Anarchistyczne FAQ (http://anarchizm.info) a translation of Anarchist FAQ (https://theanarchistlibrary.org/library/the-anarchist-faq-editorial-collective-an-anarchist-faq)
- Federacja Anarchistyczna (http://federacja-anarchistyczna.pl)
- Związek Syndykalistów Polski (https://zsp.net.pl) (Polish section of IWA-AIT)
- Komitet Obrony Praw Lokatorów (https://lokatorzy.info.pl)
- Solidarity Federation (https://solfed.org.uk)
- Priama Akcia (https://priamaakcia.sk)

Special thanks to `Working Class History <https://twitter.com/wrkclasshistory>`_ for very powerful samples that we could use in our unit tests.