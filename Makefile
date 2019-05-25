#!make

#include .env
#export $(shell sed 's/=.*//' .env)

.SILENT:

SHELL := /bin/bash

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

ENV="prod"

## This help dialog
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

## Build the application by running preparation tasks such as composer install
build:
	composer install --dev

## Prepare the application to be ready to run
deploy: build

## Migrate the database
migrate:
	./vendor/bin/phinx migrate

## Run a development web server
run_dev_server:
	COMPOSER_PROCESS_TIMEOUT=9999999 composer run web

## Run application test suites
test:
	./vendor/bin/phpunit -vvv

## Build x86_64 image
build@x86_64:
	sudo docker build . -f ./Dockerfile.x86_64 -t wolnosciowiec/uptime-admin-board
	sudo docker tag wolnosciowiec/uptime-admin-board quay.io/riotkit/uptime-admin-board

## Build arm7hf image
build@arm7hf:
	sudo docker build . -f ./Dockerfile.arm7hf -t wolnosciowiec/uptime-admin-board:arm7hf

## Push x86_64 image to registry
push@x86_64:
	sudo docker push wolnosciowiec/uptime-admin-board

## Push arm7hf image to registry
push@arm7hf:
	sudo docker push wolnosciowiec/uptime-admin-board:arm7hf
