#!/bin/bash

#
# Background data processing. It's synchronous, so it will never run twice.
#

SLEEP_TIME=${SLEEP_TIME:-300}

while true; do
    php ./bin/console background-process
    sleep "${SLEEP_TIME}"
done
