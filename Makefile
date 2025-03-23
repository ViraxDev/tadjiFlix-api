.PHONY: composer-install create-network help install jwt-keys jwt-keys-test php-cs-fixer phpstan restart bash start stop db-create db-migrate db-test-create db-test-migrate test
.DEFAULT_GOAL := help

DOCKER_ROOT=docker exec -t --user root $(shell docker ps --filter name=tadjiflix-api_app -q)
DOCKER_ROOT_I=docker exec -ti --user root $(shell docker ps --filter name=tadjiflix-api_app -q)
ARGS=10 2
GREEN = \033[32m
YELLOW = \033[33m
BLUE = \033[34m
RESET=\033[0m

bash: ## Enter container as root
	$(DOCKER_ROOT_I) bash

composer-install: ## Run composer install
	$(DOCKER_ROOT) composer install

check-code: phpstan php-cs-fixer ## Fixes code style issues and analyze PHP code for errors

create-network: ## Create network
	-docker network create app-network

install: create-network start composer-install ## Install dependencies

jwt-keys: ## Generate SSH keys for JWT
	@mkdir -p config/jwt
	$(DOCKER_ROOT_I) bin/console lexik:jwt:generate-keypair --overwrite

jwt-keys-test: ## Generate SSH keys for JWT
	@mkdir -p config/jwt
	$(DOCKER_ROOT) openssl genrsa -aes256 -passout pass:ci-test -out config/jwt/private-test.pem 4096
	$(DOCKER_ROOT) openssl rsa -passin pass:ci-test -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem

php-cs-fixer: composer-install ## Apply coding standards with php-cs-fixer
	$(DOCKER_ROOT) vendor/bin/php-cs-fixer fix

phpstan: composer-install ## Launch static code analysis
	$(DOCKER_ROOT) vendor/bin/phpstan

start: ## Start the project
	COMPOSE_PROJECT_NAME="tadjiflix-api" docker compose -f docker-compose.yml up -d --build

stop: ## Stop the project
	COMPOSE_PROJECT_NAME="tadjiflix-api" docker compose -f docker-compose.yml down

restart: stop start ## Restart the project

deploy-staging:
	docker-compose -f docker-compose.staging.yml down
	docker-compose --env-file .env.local -f docker-compose.staging.yml up -d --remove-orphans

db-create: ## Create database
	docker-compose exec mysql mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS tadjiflix_test"
	docker-compose exec mysql mysql -uroot -proot -e "GRANT ALL PRIVILEGES ON tadjiflix_test.* TO 'tadji'@'%'"

db-migrate: ## Run database migrations
	$(DOCKER_ROOT) bin/console doctrine:migrations:migrate --no-interaction

db-test-create: ## Create test database
	$(DOCKER_ROOT) bin/console doctrine:database:create --if-not-exists --env=test

db-test-migrate: ## Run database migrations for test
	$(DOCKER_ROOT) bin/console doctrine:migrations:migrate --no-interaction --env=test

db-setup: db-create db-migrate ## Setup database

db-test-setup: db-test-create db-test-migrate ## Setup test database

setup-test-env: db-test-setup ## Setup test environment

test: setup-test-env ## Run tests
	$(DOCKER_ROOT) bin/phpunit

help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
