# Tasker Bot - Telegram Task Tracker Bot

## О проекте

Tasker Bot - телеграм-бот для автоматического создания задач в таск-трекере с использованием ИИ-агента.

Бот принимает объёмное описание задачи от менеджера/аналитика, систематизирует его через ИИ-агент и автоматически
создаёт структурированную задачу в таск-трекере с привязкой к нужному пользователю.

## Архитектура

Проект использует **Clean Architecture** с модульной структурой:

```
app/
├── Controllers/        # HTTP контроллеры (Presentation Layer)
├── Framework/          # Laravel infrastructure
├── Modules/            # Бизнес-модули (Domain Layer)
│   ├── AI/            # ИИ-агент для обработки описаний
│   ├── TaskTracker/   # Интеграция с внешними трекерами
│   ├── Tasks/         # Координация создания задач
│   └── Telegram/      # Telegram Bot интеграция
└── Shared/            # Переиспользуемые компоненты
    ├── Abstracts/     # Абстрактные классы
    └── Contracts/     # Интерфейсы
```

## Разработка

### Развертывание проекта

1. **Копирование конфигурации**
    ```bash
    make env
    ```

2. **Заполнение переменных окружения**

   Отредактируйте `.env` и заполните:
   - KAITEN_API_URL
   - KAITEN_API_TOKEN
   - OPEN_ROUTER_AI_API_KEY
   - TELEGRAM_BOT_TOKEN
   - TELEGRAM_WEBHOOK_URL

3. **Установка зависимостей**
    ```bash
    composer install
    ```

4. **Инициализация проекта**
    ```bash
    make init
    ```

   Эта команда автоматически:
    - Соберёт Docker-образы
    - Применит миграции базы данных
    - Запустит приложение

5. **Установка Telegram Webhook**
    ```bash
    make set-webhook
    # или
    make set-webhook --url=<ссылка на webhook endpoint>
    ```

### Команды для разработки

#### Анализ кода с помощью PHPStan (поиск ошибок)

```bash
make lint
```

#### Автоматическое исправление стиля кода (php-cs-fixer)

```bash
make fix # проверка с исправлениями
# или
make fix-check # проверка без исправления
```

#### Рефакторинг кода с помощью Rector (автоматически)

```bash
make rector # проверка с исправлениями
# или
make rector-check # проверка без исправления
```

#### Проверка архитектурных зависимостей с помощью Deptrac

```bash
make deptrac
```

### Тестирование

#### Запуск Unit/Integration тестов

```bash
make run-tests
```

### Работа с миграциями

#### Создать новую миграцию

```bash
make make-migration
```

#### Применить миграции

```bash
make migrate
```

### Управление контейнерами

#### Запустить контейнеры

```bash
make up
```

#### Остановить контейнеры

```bash
make down
```

#### Перезапустить контейнеры

```bash
make restart
```

#### Открыть shell в контейнере приложения

```bash
make app-shell
```

#### Очистить окружение

```bash
make clean          # Удалить контейнеры и образы
make clean-volumes  # Удалить контейнеры, образы и тома
```

## Доступ к сервисам

- **Health check**: http://<APP_HOST>:<APP_PORT>/health
