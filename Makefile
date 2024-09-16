include .env

APP_CONTAINER=${APP_NAME}-backend-app-1

lc-compose:
	@cp .docker/local.compose.yml compose.yml 

dotenv:
	@cp .env.example .env

lc-setup:
	lc-compose
	cp-env

up:
	@docker-compose up -d 

up-log:
	@docker-compose up

down:
	@docker-compose down

shell:
	@docker exec -it ${APP_CONTAINER} sh

fish:
	@docker exec -it ${APP_CONTAINER} fish

tink:
	@docker exec -it ${APP_CONTAINER} php artisan tink

install:
	@docker exec -i ${APP_CONTAINER} composer install --ignore-platform-reqs
