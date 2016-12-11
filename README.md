[ ![Codeship Status for skobkin/point-tools](https://app.codeship.com/projects/bb9fe730-a175-0134-5572-12490b0b4938/status?branch=master)](https://app.codeship.com/projects/189850)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/b/skobkin/point-tools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/b/skobkin/point-tools/?branch=master)
[![Scrutinizer Code Coverage](https://scrutinizer-ci.com/b/skobkin/point-tools/badges/coverage.png?b=master)](https://scrutinizer-ci.com/b/skobkin/point-tools/?branch=master)
[![codecov](https://codecov.io/bb/skobkin/point-tools/branch/master/graph/badge.svg)](https://codecov.io/bb/skobkin/point-tools)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a54ef130-5aed-44f5-9ea9-c404a3d8db6b/mini.png)](https://insight.sensiolabs.com/projects/a54ef130-5aed-44f5-9ea9-c404a3d8db6b)

# Point Tools

Point Tools is a service which provides additional features for [Point.im](https://point.im/) microblog users.

# Installation

Application setup is quite simple:

## Getting the source code

```shell
git clone https://skobkin@bitbucket.org/skobkin/point-tools.git
cd point-tools
```

## Setting file access privileges
Set up appropriate write privileges for `app/cache` and `app/logs`.

## Installing dependencies

```shell
# В dev-среде:
composer install
# В prod-среде
composer install --no-dev --optimize-autoloader
```

After dependencies installation you will be asked for database credentials of PostgreSQL database and some other application parameters.

## Database initialization

```shell
php app/console doctrine:migrations:migrate
```

## Web assets installation

```shell
php app/console assets:install web --symlink
```

## Adding CRON jobs

```shell
crontab -e
```

You can use following jobs as an example:

```crontab
# point.skobk.in
*/10 * * * * /usr/bin/php /path/to/point-tools/app/console point:update:subscriptions --env=prod
0 0 * * * /usr/bin/php /path/to/point-tools/app/console point:update:subscriptions --all-users --env=prod
```

# Running tests

## Configure environment variables

```shell
export SYMFONY__TEST_DATABASE_USER=some_database_user
export SYMFONY__TEST_DATABASE_PASSWORD=some_database_password
export SYMFONY__TEST_DATABASE_NAME=some_database_name
export SYMFONY__TEST_DATABASE_PORT=postgresql_port
```

## Load fixtures (if needed)

```shell
php app/console doctrine:fixtures:load --no-interaction
```

## Run tests

```shell
phpunit -c app/
```