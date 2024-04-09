# SQL Migrations Bundle

A Symfony bundle for database versioning, using only plain SQL files.

Allow to maintain a database schema without using the Doctrine ORM entities system.

Compatible only with PostgreSQL configured with Doctrine DBAL.

## Installation

```bash
composer require swouters/sql-migrations-bundle
```

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
