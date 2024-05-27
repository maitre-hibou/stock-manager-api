SAIL_EXEC=./vendor/bin/sail

.DEFAULT_GOAL := start

down: prepare
	@-docker network disconnect stock-manager-api_sail local_proxy
	@${SAIL_EXEC} down -v --remove-orphans

prepare:
	@if [ ! -d vendor ]; then \
		composer install; \
	fi
	@if [ ! -f .env ]; then \
		cp .env.example .env; \
		php artisan key:generate --ansi; \
	fi

start: prepare
	@${SAIL_EXEC} up -d --force-recreate
	@-docker network connect stock-manager-api_sail local_proxy

.PHONY: down prepare start

db:
	@${SAIL_EXEC} artisan db:wipe
	@${SAIL_EXEC} artisan migrate
	@${SAIL_EXEC} artisan db:seed

frontend:
	@if [ ! -d node_modules ]; then\
		${SAIL_EXEC} npm i; \
	fi
	${SAIL_EXEC} npm run build

install: db

.PHONY: db frontend install
