#!/bin/bash

echo " >> Pre-fetching initial data"
sudo -u www-data /bin/bash -c "cd /var/www/html && php ./bin/console background-process" &
