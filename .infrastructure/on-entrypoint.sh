#!/bin/bash

sudo -u www-data /bin/bash -c "cd /var/www/html && php ./bin/console background-process"
