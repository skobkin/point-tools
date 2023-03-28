[![Build Status](https://ci.skobk.in/api/badges/skobkin/point-tools/status.svg)](https://ci.skobk.in/skobkin/point-tools)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/b/skobkin/point-tools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/b/skobkin/point-tools/?branch=master)
[![License](https://poser.pugx.org/skobkin/point-tools/license)](https://packagist.org/packages/skobkin/point-tools)

# Point Tools

Point Tools is a service which provides additional features for [Point.im](https://point.im/) microblog users.

# Installation

Application setup is quite simple:

## Getting the source code

### Via Git
```bash
git clone https://skobkin@bitbucket.org/skobkin/point-tools.git
cd point-tools
```

### Via Composer
```bash
composer create-project skobkin/point-tools -s dev
cd point-tools
```

## Setting file access privileges
Set up appropriate write privileges for `app/cache` and `app/logs`.

## Installing dependencies (not needed after installation via Composer)

```bash
# In developer environment:
composer install
# In production environment
composer install --no-dev --optimize-autoloader
```

After dependencies installation you will be asked for database credentials of PostgreSQL database and some other application parameters.

## Database initialization

```bash
php app/console doctrine:migrations:migrate
```

## Web assets installation

```bash
php app/console assets:install web --symlink
```

## Adding CRON jobs

```bash
crontab -e
```

You can use following jobs as an example:

```crontab
# point.skobk.in
*/10 * * * * /usr/bin/php /path/to/point-tools/app/console point:update:subscriptions --env=prod
0 0 * * * /usr/bin/php /path/to/point-tools/app/console point:update:subscriptions --all-users --env=prod
```

See [`app/crontab`](https://bitbucket.org/skobkin/point-tools/src/master/app/crontab) for more advanced usage.

## Setting Telegram webhook (to enable bot)

```bash
php app/console telegram:webhook set
```

## Removing Telegram webhook

```bash
php app/console telegram:webhook delete
```

# Running tests

## Configure environment variables

```bash
export SYMFONY__TEST_DATABASE_USER=some_database_user
export SYMFONY__TEST_DATABASE_PASSWORD=some_database_password
export SYMFONY__TEST_DATABASE_NAME=some_database_name
export SYMFONY__TEST_DATABASE_PORT=postgresql_port
export SYMFONY_ENV=test
```

## Load fixtures (if needed)

```bash
php app/console doctrine:fixtures:load --no-interaction
```

## Run tests

```bash
phpunit -c app/
```