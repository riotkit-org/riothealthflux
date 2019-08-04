#!make

#include .env
#export $(shell sed 's/=.*//' .env)

SHELL=/bin/bash
.SILENT:
.PHONY: help

## Colors
COLOR_RESET   = \033[0m
COLOR_INFO    = \033[32m
COLOR_COMMENT = \033[33m

ENV="prod"


help:
	@grep -E '^[a-zA-Z\-\_0-9\.@]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

build: ## Build the application by running preparation tasks such as composer install
	composer install --dev

deploy: build ## Prepare the application to be ready to run

migrate: ## Migrate the database
	./vendor/bin/phinx migrate

run_dev_server: ## Run a development web server
	COMPOSER_PROCESS_TIMEOUT=9999999 composer run web

test: ## Run application test suites
	./vendor/bin/phpunit -vvv

build@x86_64: ## Build x86_64 image
	sudo docker build . -f ./.infrastructure/Dockerfile.x86_64 -t wolnosciowiec/uptime-admin-board
	sudo docker tag wolnosciowiec/uptime-admin-board quay.io/riotkit/uptime-admin-board

push@x86_64: ## Push x86_64 image to registry
	sudo docker push wolnosciowiec/uptime-admin-board

build_frontend_locally: ## Install frontend locally
	cd src_frontend && yarn install

run_frontend_dev: ## Run development server for the frontend
	cd src_frontend && yarn serve

dev_up: ## Turn on the development environment
	cd .infrastructure && sudo docker-compose -p uab up --build
