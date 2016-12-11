[ ![Codeship Status for skobkin/point-tools](https://app.codeship.com/projects/bb9fe730-a175-0134-5572-12490b0b4938/status?branch=master)](https://app.codeship.com/projects/189850)
[![Coverage Status](https://coveralls.io/repos/bitbucket/skobkin/point-tools/badge.svg)](https://coveralls.io/bitbucket/skobkin/point-tools)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9727b3a5-8327-4622-92f1-cded14679b6b/mini.png)](https://insight.sensiolabs.com/projects/9727b3a5-8327-4622-92f1-cded14679b6b)

# Point Tools

Point Tools - это сервис предоставляющий дополнительные функции для блогов [Point.im](https://point.im/).

# Установка

Установка сервиса довольно проста:

## Получение исходников

```shell
git clone https://skobkin@bitbucket.org/skobkin/point-tools.git
cd point-tools
```

## Выставление прав
Выставьте права на запись для директорий `app/cache` и `app/logs`.

## Установка зависимостей

```shell
# В dev-среде:
composer install
# В prod-среде
composer install --no-dev --optimize-autoloader
```

После установки зависимостей у вас будут запрошены реквизиты доступа к БД PostgreSQL и данные необходимые для функционирования сервиса.

## Инициализация БД

```shell
php app/console doctrine:migrations:migrate
```

## Установка ресурсов

```shell
php app/console assets:install web --symlink
```

## Добавление задания в CRON

```shell
crontab -e
```

Вставьте в ваш файл crontab конфиг задания:

```crontab
# point.skobk.in
*/10 * * * * /usr/bin/php /path/to/point-tools/app/console point:update:subscriptions --env=prod
0 0 * * * /usr/bin/php /path/to/point-tools/app/console point:update:subscriptions --all-users --env=prod
```

