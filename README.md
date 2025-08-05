
## Подготовка контейнеров и установка пакетов

```shell
docker compose up -d
docker compose exec php composer install
docker compose exec php php artisan key:generate --ansi
docker compose exec php php artisan migrate --step
docker compose exec php composer openapi-generate
docker compose exec php composer test

```


## OpenApi документация
Nginx настроен на работу на 8988 порту.


```bash
http://127.0.0.1:8988/docs

```

