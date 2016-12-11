[ ![Codeship Status for skobkin/point-tools](https://app.codeship.com/projects/bb9fe730-a175-0134-5572-12490b0b4938/status?branch=master)](https://app.codeship.com/projects/189850)
[![codecov](https://codecov.io/bb/skobkin/point-tools/branch/master/graph/badge.svg)](https://codecov.io/bb/skobkin/point-tools)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a54ef130-5aed-44f5-9ea9-c404a3d8db6b/mini.png)](https://insight.sensiolabs.com/projects/a54ef130-5aed-44f5-9ea9-c404a3d8db6b)

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

