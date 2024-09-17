# Vbalance backend RestAPI

---

### Установка

-   Установить зависимости бэка: `composer install`
-   Сгенерировать APP_KEY: `php artisan key:generate`
-   Установить зависимости фронта: `npm i`
-   Создать локально БД и засидить данными
-   Создать символическую ссылку `php artisan storage:link`

### Команды при работе через OpenServer

-   Сбросить БД и запустить сидеры: `php artisan migrate:fresh --seed`
-   Запустить миграции `php artisan migrate`

---

### Подключить Swagger к Laravel

-   Установить пакет L5Swagger `composer require "darkaonline/l5-swagger"`
-   Добавить Swagger в проект `php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`
-   создать файл для swagger `php artisan l5-swagger:generate`
-   Установить пакет JWT-auth для авторизации в swagger `composer require tymon/jwt-auth`
    Инструкции здесь:` https://jwt-auth.readthedocs.io/en/develop/laravel-installation/`
-   публикация провайдера `php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"`
-   создает JWT-ключ в .env `php artisan jwt:secret`

### Полезное

-   показать все роуты проекта `php artisan route:list`

### Установка через docker:

-   Поднять докер `docker-compose up -d`
-   Установить папку vendor `docker exec -it brisk-meal-app composer install`
-   Опустить докер `docker-compose down`
-   Поднять докер `./vendor/bin/sail up -d`
-   Сгенерировать APP_KEY: `./vendor/bin/sail artisan key:generate`
-   Создать символическую ссылку `./vendor/bin/sail artisan storage:link`

### Команды при работе через docker:

-   Поднять докер `./vendor/bin/sail up -d`
-   Остановить докер `./vendor/bin/sail down`
-   Сбросить БД и запустить сидеры: `./vendor/bin/sail artisan migrate:fresh --seed`
-   Запустить миграции БД `./vendor/bin/sail artisan migrate`
