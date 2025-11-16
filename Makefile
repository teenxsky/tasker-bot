ENV_FILE = ./.env
COMPOSE = docker compose -f ./docker-compose.yml --env-file=$(ENV_FILE)

.PHONY: help
help:
	@echo "\033[33mДоступные команды:\033[0m"
	@echo "\033[32m%-30s\033[0m %s" | awk '{printf "\033[32m%-30s\033[0m %s\n", "init", "Полная инициализация проекта для разработки (сборка, миграции, ключ, Swagger)"}'
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

#--------------- ИНИЦИАЛИЗАЦИЯ ПРОЕКТА ---------------#

.PHONY: init
init:
	@$(MAKE) build
	@$(MAKE) migrate
	@$(MAKE) restart
	@$(MAKE) up


#--------------- ОСНОВНЫЕ КОМАНДЫ ---------------#

.PHONY: env
env: ## Создать .env файл из .env.example
	@cp .env.example .env

.PHONY: build
build: ## Собрать Docker-образы
	@$(COMPOSE) up -d --build

.PHONY: up
up: ## Запустить контейнеры Docker
	@$(COMPOSE) up -d --remove-orphans

.PHONY: down
down: ## Остановить и удалить контейнеры Docker
	@$(COMPOSE) down

.PHONY: clean
clean: ## Полностью очистить окружение (удалить контейнеры и образы)
	@$(COMPOSE) down --rmi all

.PHONY: clean-volumes
clean-volumes: ## Очистить окружение с удалением томов
	@$(COMPOSE) down -v

.PHONY: restart
restart: ## Перезапустить контейнеры Docker
	@$(COMPOSE) restart


#--------------- ПРОВЕРКА КАЧЕСТВА КОДА ---------------#

.PHONY: composer-validate
composer-validate: ## Проверка целостности зависимостей и корректности composer.json
	@$(COMPOSE) exec app php composer.phar validate

.PHONY: lint
lint: ## Анализ кода с помощью PHPStan (поиск ошибок)
	@$(COMPOSE) exec app ./vendor/bin/phpstan analyze --memory-limit=1G

.PHONY: fix
fix: ## Автоматическое исправление стиля кода (php-cs-fixer)
	@$(COMPOSE) exec app ./vendor/bin/php-cs-fixer fix

.PHONY: fix-check
fix-check: ## Проверка стиля кода без исправления (php-cs-fixer)
	@echo "Запустите 'make fix' для автоматического исправления ошибок"
	@$(COMPOSE) exec app ./vendor/bin/php-cs-fixer check

.PHONY: rector
rector: ## Рефакторинг кода с помощью Rector (автоматически)
	@$(COMPOSE) exec app ./vendor/bin/rector

.PHONY: rector-check
rector-check: ## Проверка совместимости и стиля кода с Rector (без изменений)
	@echo "Запустите 'make rector' для автоматического исправления ошибок"
	@$(COMPOSE) exec app ./vendor/bin/rector --dry-run

.PHONY: deptrac
deptrac: ## Проверка архитектурных зависимостей с помощью Deptrac
	@$(COMPOSE) exec app ./vendor/bin/deptrac analyze


#--------------- КОМАНДЫ LARAVEL ---------------#

.PHONY: install-dependencies
install-dependencies: ## Установить зависимости Laravel (composer install)
	@$(COMPOSE) exec app composer install

.PHONY: app-shell
app-shell: ## Открыть оболочку контейнера приложения
	@$(COMPOSE) exec app sh

.PHONY: make-migration
make-migration: ## Создать новую миграцию
	@$(COMPOSE) exec app php artisan make:migration

.PHONY: migrate
migrate: ## Применить миграции базы данных
	@$(COMPOSE) exec app php artisan migrate

.PHONY: generate-app-key
generate-app-key: ## Сгенерировать ключ приложения Laravel
	@$(COMPOSE) exec app php artisan key:generate
.PHONY: run-tests
run-tests: ## Запустить все тесты приложения (php artisan test)
	@$(COMPOSE) exec app php artisan test
