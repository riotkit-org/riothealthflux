#!/bin/bash

set -x

su node -c "cd /app && yarn install"

clean_up () {
    rm -rf /app/node_modules
    su node -c "cd /app && yarn install"
}

run_serve () {
    su node -c "cd /app && yarn serve"
    return $?
}

run_serve || (clean_up && run_serve) || exit 1
