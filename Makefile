
DOCKER_RUN    = docker-compose run --rm api
PHPUNIT       = ./vendor/bin/phpunit
PHPSTAN       = ./vendor/bin/phpstan
PHPINSIGHTS   = ./vendor/bin/phpinsights
ARTISAN       = php artisan

PHONY: start down install update test check-standards lint-fix help

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

start: ## start the docker services
	docker-compose up -d

down: ## down docker services
	docker-compose down

install: ## install all php libraries
	$(DOCKER_RUN) composer install

update: ## update all php libraries
	$(DOCKER_RUN) composer update

test: ## run tests
	$(DOCKER_RUN) $(PHPUNIT)

standards: ## check if code complies to standards
	$(DOCKER_RUN) $(PHPSTAN)
	$(DOCKER_RUN) $(PHPINSIGHTS)

lint-fix: ## fixes phpinsights
	$(DOCKER_RUN) $(PHPINSIGHTS) --fix

ide-helper: ## generate ide-helper files
	$(DOCKER_RUN) $(ARTISAN) ide-helper:generate

db-up: ## run migration and seed
	$(DOCKER_RUN) $(ARTISAN) migrate --seed

db-reset: ## reset and re-seed
	$(DOCKER_RUN) $(ARTISAN) migrate:refresh -seed
