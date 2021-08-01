#!/bin/bash

DOCKER_PHP = symfony-php
UID = $(shell id -u)

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

run: ## Start the containers
	docker network create symfony-network || true
	U_ID=${UID} docker-compose up -d

stop: ## Stop the containers
	U_ID=${UID} docker-compose stop

restart: ## Restart the containers
	$(MAKE) stop && $(MAKE) run

build: ## Rebuilds all the containers
	U_ID=${UID} docker-compose build

prepare: ## Runs backend commands
	$(MAKE) build && $(MAKE) run && $(MAKE) composer-install && $(MAKE) migration && $(MAKE) migrate  && $(MAKE) load-data

composer-install: ## Installs composer dependencies
	U_ID=${UID} docker exec --user ${UID} -it ${DOCKER_PHP} composer install --no-scripts --no-interaction --optimize-autoloader

ssh-php: ## Ssh's into the php container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_PHP} bash

run-test: ## Run test into the php container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_PHP} bin/phpunit tests/Controller/

load-data: ## Load dummy data into DDBB
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_PHP} /bin/sh -c "cd src/Scripts; ./loadData.php"

migration: ## make entity
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_PHP} bin/console make:migration

migrate: ## make all migrations to ddbb
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_PHP} bin/console doctrine:migrations:migrate

