#!/bin/bash

echo " >> Upgrading the application"
sudo -u www-data /bin/bash -c "cd /var/www/html && make migrate"

echo " >> Pre-fetching initial data"
sudo -u www-data /bin/bash -c "cd /var/www/html && php ./bin/console background-process" &
