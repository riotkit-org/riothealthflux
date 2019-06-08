#!/bin/bash

echo " >> Preparing permissions"
touch /var/www/html/var/db/database.sqlite3
chown www-data:www-data /var/www/html/var/db/ -R

echo " >> Upgrading the application"
sudo -u www-data /bin/bash -c "cd /var/www/html && make migrate"

echo " >> Pre-fetching initial data"
sudo -u www-data /bin/bash -c "cd /var/www/html && php ./bin/console background-process" &
