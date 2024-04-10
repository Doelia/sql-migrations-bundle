# SQL Migrations Bundle

A Symfony bundle for database versioning, using only plain SQL files.

Allow to maintain a database schema without using the Doctrine ORM entities system.

## Installation

Prequisites:
- PHP 8.3
- Symfony 6.4
- PostgreSQL using the public schema (not compatible with other databases for now)
- Doctrine DBAL 4.* configured

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require swouters/sql-migrations-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    SWouters\SqlMigrationsBundle\SqlMigrationsBundle::class => ['all' => true],
];
```

### Usage

#### Step 1: Put your .sql migration files in a `migrations` directory at the root of your project

The order of the files is important, the bundle will execute them in the order of their names.
I recommend adding the date in the filename, like `2024-03-30-[feature].sql`, to keep track of the order.

There is no down migration system.

#### Step 2: Run the command to apply the migrations

That command will execute all the SQL files in the `migrations` directory that have not been executed yet.

```bash
php bin/console sql-migrations:execute --dry-run
```

The `--dry-run` option will only display the SQL queries that will be executed. Remove the option to execute the queries.

You can add the `--drop-database` option to drop the database (the public schema) before applying the migrations (useful for local/dev environment).

That command will use an internal table `_migrations` in the database to keep track of the applied migrations. You can change the name of the table by setting the `SQL_MIGRATIONS_TABLE` environment variable.

## Development

Clone the repository :
```
git clone https://github.com/Doelia/sql-migrations-bundle.git
```

### Run tests locally

#### With Docker

The docker stack includes the PHP runtime and the PostgreSQL database.

```bash
.cloud/test.sh
```

#### Without Docker

Prequisites:
- Php 8.3 / Composer
- A running PostgreSQL database
- A dedicated database for tests

Create a `.env.test.local` file and adapt the `DATABASE_URL` variable to your configuration:
```dotenv
DATABASE_URL=pgsql://user:password@host:port/database
```

Run tests:
```bash
composer update
vendor/bin/phpunit
```
