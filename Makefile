#!/bin/bash

APP = kontratazioa_php_8
VERSION := $(shell cat ./VERSION)
DOCKER_REPO_NGINX = ikerib/${APP}_nginx:${VERSION}
DOCKER_REPO_APP = ikerib/${APP}_app:${VERSION}
USER_ID = $(shell id -u)
GROUP_ID= $(shell id -g)
user==www-data

help:
	@echo 'usage: make [target]'
	@echo
	@echo 'targets'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ":#"

build:
	docker compose --env-file .env.local build

build-force:
	docker compose --env-file .env.local build --force-rm --no-cache

restart:
	$(MAKE) stop && $(MAKE) run

run:
	docker compose --env-file .env.local up -d

stop:
	docker compose down

ssh:
	docker compose exec app zsh

fixtures:
	docker compose exec app ./bin/console doctrine:fixtures:load --no-interaction
	docker compose exec app ./bin/console app:import:01-guneak doc/csv/guneak.txt
	docker compose exec app ./bin/console app:import:02-bezeroa doc/csv/bezeroa.txt
	docker compose exec app ./bin/console app:import:03-ibilbidea doc/csv/ibilbidea.txt
	docker compose exec app ./bin/console app:import:04-eguraldia doc/csv/eguraldia.txt
	docker compose exec app ./bin/console app:import:05-zigorra doc/csv/zigorrak.txt
	docker compose exec app ./bin/console app:import:06-bizikleta doc/csv/bizikleta.txt
	docker compose exec app ./bin/console app:import:07-mailegua doc/csv/maileguak.txt
