# Vbalance backend RestAPI

## Local SetUP
### Requirements
 - Docker + Docker-compose
 - Telegram bot for testing authentification

### Run with Docker-compose
#### 1. Скопировать и переименовать:
 - `{ProjectRoot}/.docker/local.compose.yml` -> `{ProjectRoot}/compose.yml`
 - `{ProjectRoot}/.env.example` -> `{ProjectRoot}/.env`

```bash
mv .docker/local.compose.yml compose.ym
mv .env.example .env
```
 
#### 2. Указать конфигурацию в .env
 - APP_URL - для построения ссылок
 - FRONTEND_URL - для построения ссылок к фронту
 - MAIL_* -  Параметры для отправки mail
 - TELEGRAM_* - Параметры для тестирования авторизации, в настройках телеграм бота необходимо указать домен с которого будет переадресация to TG.

#### 3. Собрать дев окружения
```bash
docker-compose build;
docker-compose up;
``` 

### Websockets
Пример реализации события в `app/Events/Test/WebsocketTestEvent.php`

Формат событий: 'название-канала.событие'