#!/bin/bash

#
# Background data processing. It's synchronous, so it will never run twice.
#

SLEEP_TIME=300

while true; do
    cd /var/www/html && php ./bin/console background-process
    sleep ${SLEEP_TIME}
done
