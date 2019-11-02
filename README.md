RiotKit's Server Uptime Admin Board
===================================

The goal of the project was to have a single panel for each monitoring service.

**Creator: I'm a social SysOps/DevOps of many non-profit projects, they all have separated accounts, so for me
it's better to have everything glued together.**

Created for the Anarchist movement.

Special thanks:
- Cory Kennedy-Darby (we forked his `ckdarby/php-uptimerobot` library on MIT license)

Running
=======

Bare metal - Automatically
--------------------------

```bash
make deploy
```

Bare metal - Manually
---------------------

```bash
# installing
composer install
cd src_frontend && yarn install && cd ..

# updating
./vendor/bin/phinx migrate

# generating statistics
php ./bin/console background-process
```

Running with docker-compose
---------------------------

Below is an example configuration for docker-compose v2.x

```yaml
version: "2.3"
services:
    app_dashboard:
        image: quay.io/riotkit/uptime-admin-board:v2.1.0
        environment:
            - UAB_PROVIDERS=UptimeRobot://xyz
            - UAB_TITLE=RiotKit Collective monitoring
            - UAB_EXPOSE_URLS=1
            - UAB_HISTORY_MAX_DAYS=3
            - SLEEP_TIME=600
    
            # gateway configuration
            - VIRTUAL_HOST=dashboard.riotkit.org${DOMAIN_SUFFIX}
            - VIRTUAL_PORT=80
            - LETSENCRYPT_HOST=dashboard.riotkit.org${DOMAIN_SUFFIX}
            - LETSENCRYPT_EMAIL=${LETSENCRYPT_EMAIL}
        labels:
            org.riotkit.dashboard.enabled: true
            org.riotkit.dashboard.description: 'Applications health monitoring'
            org.riotkit.dashboard.icon: 'pe-7s-angle-down-circle'
            org.riotkit.dashboard.only_for_admin: false
            com.centurylinklabs.watchtower.enable: false
```


Configuration
=============

To set a configuration variable you need to set an environment variable listed below.
The environment variables can be set on `nginx`, `apache's .htaccess`, `docker` or in a shell in test run.

```bash
#
# Uptime information providers
# ----------------------------
#   Supported: Uptime Robot (format: UptimeRobot://token)
#
export UAB_PROVIDERS=UptimeRobot://some-token-here;UptimeRobot://some-other-token

#
# Text to display in the panel
#
export UAB_TITLE="My System Monitoring"

#
# CSS theme
#
export UAB_CSS=./assets/css/zapatista.css

#
# Should the URLS pointing to healthchecks be visible?
#   If the dashboard is public then value can be set to 0
#
export UAB_EXPOSE_URLS=1

#
# Logging
#
UAB_LOG_PATH=/var/log/uptime-admin-board.log

#
# Cache support
# -------------
#   By default the application is using a file cache in the ./var/cache directory
#   and the freshness is 60 seconds. The redis could be also used there.
#
export UAB_CACHE_ID=test123 # optional
export UAB_CACHE_TTL=60     # default 60 seconds
export UAB_CACHE=file       # defaults to "file", available: file|redis

#
# Redis support
# -------------
#   When UAB_CACHE=redis, then those settings are required to be correct
#
export UAB_REDIS_HOST=localhost                  # optional, defaults to "localhost"
export UAB_REDIS_PORT=6379                       # optional, defauts to 6379
export UAB_REDIS_PREFIX=org.riotkit.dashboard_   # redis keys prefix

#
# TOR support
# -----------
#   Allows to switch exit node on each request if configured
#
export UAB_TOR_MANAGEMENT_PORT=9052  # optional, defaults to 9052
export UAB_TOR_PASSWORD=""           # optional, TOR management password

export UAB_STATS_ENABLED=1           # enable statistics generation?
export UAB_DYNAMIC_DASHBOARD=1       # simple HTML dashboard or Vue.js powered dynamic dashboard

#
# Proxy Support
# -------------
#   Can be used with TOR (is required by TOR) but don't have to
#
export UAB_PROXY="http://someproxy:9080" # optional, a HTTP proxy to use, if it's a TOR proxy you can also use the rest config variables to reset the IP address for each request

#
# Other
#
export UAB_HISTORY_MAX_DAYS=5   # max days to keep in the history
export UAB_DB_PATH=./var/db/database.sqlite3
```

Docker extra variables
----------------------

```bash
# interval for the statistics generation
export SLEEP_TIME=300 
```


Testing in developer environment
================================

You can run this project very quickly with a PHP Built-in webserver.

_**NOTICE:** This is not a production-environment ready method to run the application_

```
make run_dev_server
```

Unit tests
==========

```
make test
```

Used software
=============

- Frontend: Special thanks to https://github.com/DesignRevision/shards-dashboard-vue project
